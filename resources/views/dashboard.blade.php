<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Cards de acceso rápido --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <a href="{{ route('admin.projects.index') }}" 
                    class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                        📁 Mis Proyectos
                    </h5>
                    <p class="font-normal text-gray-700">
                        Ver y gestionar todos tus proyectos
                    </p>
                </a>

                <a href="{{ route('admin.projects.create') }}" 
                   class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 transition">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">
                        ✨ Nuevo Proyecto
                    </h5>
                    <p class="font-normal text-gray-700">
                        Crear un nuevo proyecto desde cero
                    </p>
                </a>
            </div>

            @php
                $recentProjects = \App\Models\Project::orderBy('created_at', 'desc')->limit(8)->get();
            @endphp

            @if($recentProjects->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-lg font-semibold text-gray-900">Proyectos Recientes</h4>
                        <a href="{{ route('admin.projects.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Ver todos →
                        </a>
                    </div>
                    
                    {{-- Grid de proyectos --}}
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                        @foreach($recentProjects as $project)
                            <div class="group relative bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:border-indigo-500 transition-all hover:shadow-lg">
                                {{-- Imagen del proyecto --}}
                                <div style="width: 100%; height: 200px; overflow: hidden; background-color: #f3f4f6;">
                                    @if($project->featured_image)
                                        <img src="{{ asset('storage/' . $project->featured_image) }}" 
                                             alt="{{ $project->title }}" 
                                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;"
                                             onmouseover="this.style.transform='scale(1.05)'" 
                                             onmouseout="this.style.transform='scale(1)'">
                                    @else
                                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 3rem;">
                                            📄
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- Info del proyecto --}}
                                <div class="p-4">
                                    <h5 class="font-semibold text-gray-900 mb-2 truncate" title="{{ $project->title }}">
                                        {{ $project->title }}
                                    </h5>
                                    
                                    <p class="text-sm text-gray-600 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ $project->short_description }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                        @if($project->client)
                                            <span>👤 {{ $project->client }}</span>
                                        @endif
                                        @if($project->year)
                                            <span>📅 {{ $project->year }}</span>
                                        @endif
                                    </div>
                                    
                                    {{-- Estado y botón --}}
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $project->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $project->is_published ? 'Publicado' : 'Borrador' }}
                                        </span>
                                        
                                        <a href="{{ route('admin.projects.edit', $project) }}" 
                                           title="Editar proyecto"
                                           class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded transition"
                                           style="background-color: #4f46e5; color: white;"
                                           onmouseover="this.style.backgroundColor='#4338ca'" 
                                           onmouseout="this.style.backgroundColor='#4f46e5'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            Editar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="mb-4">
                            <span style="font-size: 4rem;">📁</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No hay proyectos aún</h3>
                        <p class="text-gray-600 mb-4">¡Crea tu primer proyecto para empezar!</p>
                        <a href="{{ route('admin.projects.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest transition"
                           style="background-color: #4f46e5;"
                           onmouseover="this.style.backgroundColor='#4338ca'" 
                           onmouseout="this.style.backgroundColor='#4f46e5'">
                            Crear Primer Proyecto
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>