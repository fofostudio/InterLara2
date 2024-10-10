<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $authenticatedUser = auth()->user();

        if ($authenticatedUser->hasRole('superadmin')) {
            $users = User::with('role', 'point')->get();
            $totalMaxUsers = Point::sum('maxusers');
        } else {
            $users = User::with('role')
                ->where('point_id', $authenticatedUser->point_id)
                ->get();
            $totalMaxUsers = $authenticatedUser->point->maxusers;
        }

        $roles = Role::whereNotIn('name', ['superadmin'])
            ->orderBy('name', 'asc')
            ->get();

        // Calcular estadísticas
        $totalUsers = $users->count();
        $availableUsers = $totalMaxUsers - $totalUsers;
        $usersByRole = $users->groupBy('role.description')->map->count();

        return view('user.index', compact('users', 'roles', 'availableUsers', 'totalMaxUsers', 'totalUsers', 'usersByRole'));
    }


    public function store(Request $request)
    {
        $authenticatedUser = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'point_id' => [
                $authenticatedUser->hasRole('superadmin') ? 'required' : 'nullable',
                'exists:points,id'
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $userData = $validator->validated();

            // Determinar el punto_id
            $pointId = $authenticatedUser->hasRole('superadmin')
                ? $userData['point_id']
                : $authenticatedUser->point_id;

            // Verificar el límite de usuarios para el punto
            $point = Point::findOrFail($pointId);
            $currentUserCount = User::where('point_id', $pointId)->count();

            if ($currentUserCount >= $point->maxusers) {
                return response()->json([
                    'error' => 'No se pueden crear más usuarios para este punto. Se ha alcanzado el límite máximo.'
                ], 422);
            }

            // Verificar si el rol es de operador
            $operatorRole = Role::where('name', 'operator')->first();
            if ($userData['role_id'] == $operatorRole->id) {
                // Si es operador, establecer la contraseña predeterminada
                $userData['password'] = 'InterLara';
            }

            $userData['password'] = Hash::make($userData['password']);
            $userData['point_id'] = $pointId;

            $user = User::create($userData);

            return response()->json([
                'message' => 'Usuario creado exitosamente.',
                'user' => $user->load('role', 'point')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear el usuario: ' . $e->getMessage()], 500);
        }
    }
    public function update(Request $request, User $user)
    {
        $authenticatedUser = auth()->user();

        if (!$authenticatedUser->hasRole('superadmin') && $user->point_id !== $authenticatedUser->point_id) {
            if ($request->ajax()) {
                return response()->json(['error' => 'No tienes permiso para editar este usuario.'], 403);
            }
            return redirect()->back()->with('error', 'No tienes permiso para editar este usuario.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'point_id' => [
                $authenticatedUser->hasRole('superadmin') ? 'required' : 'nullable',
                'exists:points,id'
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Add validation for avatar
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $userData = $validator->validated();

            if (!empty($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            } else {
                unset($userData['password']);
            }

            if (!$authenticatedUser->hasRole('superadmin')) {
                unset($userData['point_id']);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $userData['avatar'] = $avatarPath;
            }

            $user->update($userData);

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Usuario actualizado exitosamente.',
                    'user' => $user->load('role', 'point')
                ]);
            }

            return redirect()->route('user.index')
                ->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al actualizar el usuario: ' . $e->getMessage())->withInput();
        }
    }
    public function edit(User $user)
    {
        $authenticatedUser = auth()->user();

        if (!$authenticatedUser->hasRole('superadmin') && $user->point_id !== $authenticatedUser->point_id) {
            return response()->json(['error' => 'No tienes permiso para editar este usuario.'], 403);
        }

        return response()->json($user->load('role', 'point'));
    }

    public function destroy(User $user)
    {
        $authenticatedUser = auth()->user();

        if ($user->id === $authenticatedUser->id) {
            return response()->json(['error' => 'No puedes eliminar tu propio usuario.'], 403);
        }

        if (!$authenticatedUser->hasRole('superadmin') && $user->point_id !== $authenticatedUser->point_id) {
            return response()->json(['error' => 'No tienes permiso para eliminar este usuario.'], 403);
        }

        try {
            $user->delete();
            return response()->json(['message' => 'Usuario eliminado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar el usuario.'], 500);
        }
    }
}
