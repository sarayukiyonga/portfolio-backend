{{-- resources/views/admin/users/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Usuario: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nombre --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Nombre <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $user->name) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                   required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   value="{{ old('email', $user->email) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('email') border-red-500 @enderror"
                                   required>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Contraseña --}}
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Nueva Contraseña
                            </label>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                Dejar en blanco para mantener la contraseña actual
                            </p>
                        </div>

                        {{-- Confirmar Contraseña --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                Confirmar Nueva Contraseña
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Roles --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Roles Asignados
                            </label>
                            <div class="space-y-2">
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->id }}"
                                               id="role_{{ $role->id }}"
                                               {{ $user->roles->contains($role->id) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="role_{{ $role->id }}" class="ml-2 block text-sm text-gray-900">
                                            <span class="font-medium">{{ ucfirst($role->name) }}</span>
                                            @if($role->description)
                                                <span class="text-gray-500">- {{ $role->description }}</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Información adicional --}}
                        <div class="mb-6 p-4 bg-gray-50 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">ℹ️ Información del usuario:</h4>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li><strong>Registrado:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</li>
                                <li><strong>Última actualización:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</li>
                                <li><strong>Roles actuales:</strong> 
                                    @if($user->roles->count() > 0)
                                        {{ $user->roles->pluck('name')->map(fn($name) => ucfirst($name))->join(', ') }}
                                    @else
                                        Sin roles asignados
                                    @endif
                                </li>
                            </ul>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.users.index') }}" 
                               style="background-color: #d1d5db; color: #1f2937; padding: 0.5rem 1rem; border-radius: 0.375rem; text-decoration: none; font-weight: bold; display: inline-block;"
                               onmouseover="this.style.backgroundColor='#9ca3af'" 
                               onmouseout="this.style.backgroundColor='#d1d5db'">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="background-color: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: bold; border: none; cursor: pointer;"
                                    onmouseover="this.style.backgroundColor='#2563eb'" 
                                    onmouseout="this.style.backgroundColor='#3b82f6'">
                                Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
