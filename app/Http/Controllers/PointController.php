<?php

namespace App\Http\Controllers;

use App\Models\Point;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PointController extends Controller
{
    public function index()
    {
        $points = Point::with('responsibleUser')->get();
        return view('superadmin.points.index', compact('points'));
    }

    public function create()
    {
        return view('superadmin.points.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|unique:points|max:255',
            'description' => 'nullable',
            'address' => 'required',
            'phone' => 'nullable',
            'maxusers' => 'nullable|integer',
            'dateStart' => 'nullable|date',
            'dateLimit' => 'nullable|date|after:dateStart',
            'status' => 'required|in:active,inactive',
            'resp_user_name' => 'required|string|max:255',
            'resp_user_email' => 'required|string|email|max:255|unique:users,email',
            'resp_user_password' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $point = Point::create($request->except(['resp_user_name', 'resp_user_email', 'resp_user_password']));

            $adminRole = Role::where('name', 'admin')->first();

            $user = User::create([
                'name' => $request->resp_user_name,
                'email' => $request->resp_user_email,
                'password' => Hash::make($request->resp_user_password),
                'role_id' => $adminRole->id,
                'point_id' => $point->id,
            ]);

            $point->resp_user = $user->id;
            $point->save();

            DB::commit();

            return redirect()->route('superadmin.points.index')
                ->with('success', 'Punto y usuario responsable creados exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al crear el punto y el usuario: ' . $e->getMessage());
        }
    }

    public function show(Point $point)
    {
        $availableUsers = User::whereDoesntHave('point')->get();
        return view('superadmin.points.show', compact('point', 'availableUsers'));
    }

    public function edit(Point $point)
    {
        return view('superadmin.points.edit', compact('point'));
    }

    public function update(Request $request, Point $point)
    {
        $request->validate([
            'number' => 'required|unique:points,number,' . $point->id . '|max:255',
            'description' => 'nullable',
            'address' => 'required',
            'phone' => 'nullable',
            'maxusers' => 'nullable|integer',
            'dateStart' => 'nullable|date',
            'dateLimit' => 'nullable|date|after:dateStart',
            'status' => 'required|in:active,inactive',
        ]);

        $point->update($request->all());

        return redirect()->route('superadmin.points.index')
            ->with('success', 'Punto actualizado exitosamente.');
    }

    public function destroy(Point $point)
    {
        $point->delete();

        return redirect()->route('superadmin.points.index')
            ->with('success', 'Punto eliminado exitosamente.');
    }

    public function assignUser(Request $request, Point $point)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->point()->associate($point);
        $user->save();

        return redirect()->route('superadmin.points.show', $point->id)
            ->with('success', 'Usuario asignado exitosamente.');
    }

    public function removeUser(Point $point, User $user)
    {
        $user->point()->dissociate();
        $user->save();

        return redirect()->route('superadmin.points.show', $point->id)
            ->with('success', 'Usuario removido exitosamente.');
    }
}