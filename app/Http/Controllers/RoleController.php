<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    /**
     * Mostrar lista de roles
     */
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Guardar nuevo rol
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name|alpha_dash',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'El nombre del rol es obligatorio',
            'name.unique' => 'Este rol ya existe',
            'name.alpha_dash' => 'El nombre solo puede contener letras, números, guiones y guiones bajos',
        ]);

        // Convertir el nombre a minúsculas y sin espacios
        $validated['name'] = strtolower(str_replace(' ', '_', $validated['name']));

        Role::create($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol creado correctamente');
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Role $role)
    {
        // Prevenir edición del rol admin
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No se puede editar el rol de administrador');
        }

        return view('admin.roles.edit', compact('role'));
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, Role $role)
    {
        // Prevenir edición del rol admin
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No se puede editar el rol de administrador');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|alpha_dash|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'El nombre del rol es obligatorio',
            'name.unique' => 'Este rol ya existe',
            'name.alpha_dash' => 'El nombre solo puede contener letras, números, guiones y guiones bajos',
        ]);

        // Convertir el nombre a minúsculas y sin espacios
        $validated['name'] = strtolower(str_replace(' ', '_', $validated['name']));

        $role->update($validated);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Rol actualizado correctamente');
    }

    /**
     * Eliminar rol
     */
    public function destroy(Role $role)
    {
        // Prevenir eliminación del rol admin
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No se puede eliminar el rol de administrador');
        }

        // Verificar si hay usuarios con este rol
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'No se puede eliminar el rol porque hay usuarios asignados a él');
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Rol '{$roleName}' eliminado correctamente");
    }
}
