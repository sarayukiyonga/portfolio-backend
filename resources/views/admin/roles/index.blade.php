{{-- resources/views/admin/roles/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Roles') }}
            </h2>
            <a href="{{ route('admin.roles.create') }}" 
               style="background-color: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; text-decoration: none; font-weight: bold; display: inline-block;"
               onmouseover="this.style.backgroundColor='#2563eb'" 
               onmouseout="this.style.backgroundColor='#3b82f6'">
                Crear Nuevo Rol
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes de éxito/error --}}
            @if(session('success'))
                <div style="background-color: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;" role="alert">
                    <span style="display: block;">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background-color: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem;" role="alert">
                    <span style="display: block;">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nombre del Rol
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Descripción
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Usuarios Asignados
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($roles as $role)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ ucfirst($role->name) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            {{ $role->description ?? 'Sin descripción' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $role->users_count }} usuario(s)
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($role->name !== 'admin')
                                            <a href="{{ route('admin.roles.edit', $role) }}" 
                                               style="color: #4f46e5; text-decoration: none; margin-right: 1rem;"
                                               onmouseover="this.style.color='#4338ca'"
                                               onmouseout="this.style.color='#4f46e5'">
                                                Editar
                                            </a>
                                            
                                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        style="color: #dc2626; background: none; border: none; cursor: pointer; padding: 0;"
                                                        onmouseover="this.style.color='#991b1b'"
                                                        onmouseout="this.style.color='#dc2626'"
                                                        onclick="return confirm('¿Estás seguro de eliminar este rol?')">
                                                    Eliminar
                                                </button>
                                            </form>
                                        @else
                                            <span style="color: #9ca3af;">Protegido</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No hay roles registrados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.users.index') }}" 
                   style="color: #2563eb; text-decoration: none;"
                   onmouseover="this.style.color='#1d4ed8'"
                   onmouseout="this.style.color='#2563eb'">
                    ← Volver a gestión de usuarios
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
