<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('superadmin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('superadmin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles|max:255',
            'description' => 'nullable',
        ]);

        Role::create($request->all());

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    public function edit(Role $role)
    {
        return view('superadmin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id . '|max:255',
            'description' => 'nullable',
        ]);

        $role->update($request->all());

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'Rol actualizado exitosamente.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('superadmin.roles.index')
            ->with('success', 'Rol eliminado exitosamente.');
    }
}