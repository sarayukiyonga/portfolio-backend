{{-- resources/views/admin/roles/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Rol: ') . ucfirst($role->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nombre del rol --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nombre del Rol <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $role->name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Use solo letras minúsculas, números, guiones (-) o guiones bajos (_). Sin espacios.
                            </p>
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Descripción
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                                      placeholder="Describe los permisos y funciones de este rol">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Información adicional --}}
                        <div class="mb-6 p-4 bg-gray-50 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">ℹ️ Información del rol:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><strong>Usuarios asignados:</strong> {{ $role->users()->count() }}</li>
                                <li><strong>Creado:</strong> {{ $role->created_at->format('d/m/Y H:i') }}</li>
                                <li><strong>Última actualización:</strong> {{ $role->updated_at->format('d/m/Y H:i') }}</li>
                            </ul>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.roles.index') }}" 
                               style="background-color: #d1d5db; color: #1f2937; padding: 0.5rem 1rem; border-radius: 0.375rem; text-decoration: none; font-weight: bold; display: inline-block;"
                               onmouseover="this.style.backgroundColor='#9ca3af'" 
                               onmouseout="this.style.backgroundColor='#d1d5db'">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="background-color: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: bold; border: none; cursor: pointer;"
                                    onmouseover="this.style.backgroundColor='#2563eb'" 
                                    onmouseout="this.style.backgroundColor='#3b82f6'">
                                Actualizar Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
