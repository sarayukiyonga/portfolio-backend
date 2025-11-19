<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Proyecto: {{ $project->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORMULARIO PRINCIPAL DE ACTUALIZACIÓN --}}
                    <form action="{{ route('admin.projects.update', $project) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Título --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                                Título del Proyecto
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        {{-- Descripción Corta --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="short_description">
                                Descripción Corta
                            </label>
                            <textarea name="short_description" id="short_description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('short_description', $project->short_description) }}</textarea>
                        </div>

                        {{-- Caso de Estudio --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="case_study">
                                Caso de Estudio Completo
                            </label>
                            <textarea name="case_study" id="case_study" class="tinymce-full">{{ old('case_study', $project->case_study) }}</textarea>
                        </div>

                        {{-- Imagen Principal Actual --}}
                        @if($project->featured_image)
                            <div class="mb-4 p-3 bg-gray-50 rounded border-2 border-gray-200" id="featured-image-container">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   name="delete_featured_image" 
                                                   value="1"
                                                   class="rounded border-gray-300 text-red-600 shadow-sm"
                                                   onchange="document.getElementById('featured-image-container').classList.toggle('border-red-500', this.checked)">
                                            <span class="ml-2 text-sm text-red-600 font-semibold">🗑️ Eliminar</span>
                                        </label>
                                    </div>
                                    
                                    <div class="flex-grow">
                                        <p class="text-sm font-bold text-gray-700 mb-2">Imagen Principal Actual:</p>
                                        <img src="{{ asset('storage/' . $project->featured_image) }}" alt="{{ $project->title }}" class="w-48 h-auto rounded shadow">
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Cambiar Imagen Principal --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="featured_image">
                                {{ $project->featured_image ? 'Cambiar Imagen Principal' : 'Subir Imagen Principal' }}
                            </label>
                            <input type="file" name="featured_image" id="featured_image" accept="image/*,video/mp4,video/webm" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        {{-- Bloques de Contenido Dinámicos --}}
                        <div class="mb-6 p-4 bg-blue-50 rounded">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-700">Bloques de Contenido</h3>
                                    <p class="text-sm text-gray-600">Añade imágenes/videos con texto que se distribuirán en el caso de estudio</p>
                                </div>
                                <button type="button" 
                                        onclick="addContentBlock()" 
                                        style="background-color: #16a34a; color: white; font-weight: bold; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem;"
                                        onmouseover="this.style.backgroundColor='#15803d'" 
                                        onmouseout="this.style.backgroundColor='#16a34a'">
                                    + Añadir Bloque
                                </button>
                            </div>
                            
                            <div id="content-blocks-container">
                                {{-- Mostrar bloques existentes --}}
                                @if($project->content_blocks)
                                    @foreach($project->content_blocks as $index => $block)
    <div class="content-block mb-4 p-4 bg-white rounded border-2 border-gray-300" data-existing-index="{{ $index }}">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold text-gray-700">Bloque de Contenido {{ $index + 1 }}</h4>
            <button type="button" 
                    onclick="removeExistingContentBlock(this, {{ $index }})" 
                    style="background-color: #dc2626; color: white; font-weight: bold; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem;"
                    onmouseover="this.style.backgroundColor='#b91c1c'" 
                    onmouseout="this.style.backgroundColor='#dc2626'">
                🗑️ Eliminar
            </button>
        </div>
        
        {{-- Grid para mostrar imágenes actuales y campos de subida --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
            
            {{-- Columna 1: Imagen Principal --}}
            <div>
                @if(isset($block['file']))
                    <div class="mb-3 p-2 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600 mb-2">Archivo principal:</p>
                        @php
                            $extension = pathinfo($block['file'], PATHINFO_EXTENSION);
                            $isVideo = in_array(strtolower($extension), ['mp4', 'webm']);
                        @endphp
                        
                        @if($isVideo)
                            <video controls style="width: 100%; height: 150px; object-fit: contain; border-radius: 8px;">
                                <source src="{{ asset('storage/' . $block['file']) }}" type="video/{{ $extension }}">
                            </video>
                        @else
                            <img src="{{ asset('storage/' . $block['file']) }}" 
                                 alt="Contenido {{ $index + 1 }}" 
                                 style="width: 100%; height: 150px; object-fit: contain; border-radius: 8px;">
                        @endif
                        
                        <input type="hidden" name="existing_blocks[{{ $index }}][file]" value="{{ $block['file'] }}">
                    </div>
                @endif
                
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    {{ isset($block['file']) ? 'Cambiar principal' : 'Subir principal' }}
                </label>
                <input type="file" 
                       name="existing_blocks[{{ $index }}][new_file]" 
                       accept="image/*,video/mp4,video/webm"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            {{-- Columna 2: Imagen Hover --}}
            <div>
                @if(isset($block['file_hover']))
                    <div class="mb-3 p-2 bg-gray-50 rounded">
                        <p class="text-sm text-gray-600 mb-2">Archivo hover:</p>
                        @php
                            $extension = pathinfo($block['file_hover'], PATHINFO_EXTENSION);
                            $isVideo = in_array(strtolower($extension), ['mp4', 'webm']);
                        @endphp
                        
                        @if($isVideo)
                            <video controls style="width: 100%; height: 150px; object-fit: contain; border-radius: 8px;">
                                <source src="{{ asset('storage/' . $block['file_hover']) }}" type="video/{{ $extension }}">
                            </video>
                        @else
                            <img src="{{ asset('storage/' . $block['file_hover']) }}" 
                                 alt="Hover {{ $index + 1 }}" 
                                 style="width: 100%; height: 150px; object-fit: contain; border-radius: 8px;">
                        @endif
                        
                        <input type="hidden" name="existing_blocks[{{ $index }}][file_hover]" value="{{ $block['file_hover'] }}">
                    </div>
                @endif
                
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    {{ isset($block['file_hover']) ? 'Cambiar hover' : 'Subir hover' }}
                </label>
                <input type="file" 
                       name="existing_blocks[{{ $index }}][new_file_hover]" 
                       accept="image/*,video/mp4,video/webm"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-600 text-xs italic mt-1">Al pasar el mouse</p>
            </div>
        </div>
        
        {{-- Texto (ocupa todo el ancho) --}}
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Texto
            </label>
            <textarea name="existing_blocks[{{ $index }}][text]" 
                      class="tinymce-simple shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                      rows="3">{{ old("existing_blocks.$index.text", $block['text'] ?? '') }}</textarea>
        </div>
        
        <input type="hidden" name="existing_blocks[{{ $index }}][index]" value="{{ $index }}">
    </div>
@endforeach
                                @endif
                            </div>
                        </div>

                        {{-- Template para nuevos bloques --}}
                        <template id="content-block-template">
    <div class="content-block mb-4 p-4 bg-white rounded border-2 border-gray-300">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold text-gray-700">Nuevo Bloque de Contenido</h4>
            <button type="button" 
                    onclick="removeContentBlock(this)" 
                    style="background-color: #dc2626; color: white; font-weight: bold; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem;"
                    onmouseover="this.style.backgroundColor='#b91c1c'" 
                    onmouseout="this.style.backgroundColor='#dc2626'">
                🗑️ Eliminar
            </button>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Imagen/Video Principal
                </label>
                <input type="file" 
                       name="new_content_blocks_files[]" 
                       accept="image/*,video/mp4,video/webm"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-600 text-xs italic mt-1">Se muestra por defecto</p>
            </div>
            
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Imagen/Video al Hacer Hover
                </label>
                <input type="file" 
                       name="new_content_blocks_files_hover[]" 
                       accept="image/*,video/mp4,video/webm"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-600 text-xs italic mt-1">Al pasar el mouse</p>
            </div>
        </div>
        
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Texto
            </label>
            <textarea name="new_content_blocks_texts[]" 
                      rows="3"
                      placeholder="Describe este contenido..."
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>
    </div>
</template>

                        {{-- Galería Actual --}}
                        @if($project->gallery && count($project->gallery) > 0)
                            <div class="mb-4 p-3 bg-gray-50 rounded">
                                <p class="text-sm font-bold text-gray-700 mb-3">Galería Actual:</p>
                                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
                                    @foreach($project->gallery as $index => $file)
                                        <div class="border-2 border-gray-200 rounded p-2" id="gallery-item-{{ $index }}">
                                            <label class="flex items-center cursor-pointer mb-2">
                                                <input type="checkbox" 
                                                       name="delete_gallery[]" 
                                                       value="{{ $file }}"
                                                       class="rounded border-gray-300 text-red-600 shadow-sm"
                                                       onchange="document.getElementById('gallery-item-{{ $index }}').classList.toggle('border-red-500', this.checked)">
                                                <span class="ml-2 text-xs text-red-600 font-semibold">🗑️</span>
                                            </label>
                                            
                                            @php
                                                $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                $isVideo = in_array(strtolower($extension), ['mp4', 'webm']);
                                            @endphp
                                            
                                            @if($isVideo)
                                                <video style="width: 100%; height: 120px; object-fit: contain; border-radius: 8px;" controls>
                                                    <source src="{{ asset('storage/' . $file) }}" type="video/{{ $extension }}">
                                                </video>
                                            @else
                                                <img src="{{ asset('storage/' . $file) }}" 
                                                     alt="Galería" 
                                                     style="width: 100%; height: 120px; object-fit: contain; border-radius: 8px;">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Añadir nuevos archivos a la Galería con Drag & Drop --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Añadir Más Archivos a la Galería
                            </label>
                            
                            <input type="file" 
                                   id="gallery-input"
                                   name="gallery[]" 
                                   accept="image/*,video/mp4,video/webm" 
                                   multiple 
                                   style="display: none;">
                            
                            <div id="drop-zone" 
                                 class="border-2 border-dashed rounded-lg p-8 text-center transition-colors"
                                 style="background-color: #f9fafb; border-color: #d1d5db; height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center; cursor: pointer;">
                                <div class="mb-4">
                                    <svg class="mx-auto h-12 w-12" style="color: #9ca3af;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <p class="text-sm mb-2" style="color: #4b5563;">
                                    <span style="font-weight: 600;">Arrastra archivos aquí</span> o haz click para seleccionar
                                </p>
                                <p class="text-xs" style="color: #6b7280;">
                                    Imágenes o videos (PNG, JPG, MP4, WebM) hasta 10MB
                                </p>
                            </div>
                            
                            <div id="gallery-preview" style="margin-top: 1rem; display: none; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;"></div>
                        </div>

                        {{-- Cliente --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="client">
                                Cliente
                            </label>
                            <input type="text" name="client" id="client" value="{{ old('client', $project->client) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        {{-- Año --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="year">
                                Año
                            </label>
                            <input type="text" name="year" id="year" value="{{ old('year', $project->year) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        {{-- Publicado --}}
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_published" value="1" {{ old('is_published', $project->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                <span class="ml-2 text-sm text-gray-600">Publicar proyecto</span>
                            </label>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-between">
                            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                Actualizar Proyecto
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
let blockCount = {{ $project->content_blocks ? count($project->content_blocks) : 0 }};
let tinyMceReady = false;

document.addEventListener('DOMContentLoaded', function() {
    if (typeof tinymce !== 'undefined') {
        tinyMceReady = true;
        
        setTimeout(() => {
            tinymce.init({
                selector: '.tinymce-simple',
                height: 200,
                menubar: false,
                language: 'es',
                plugins: ['lists', 'link', 'autolink', 'code'],
                toolbar: 'undo redo | blocks | bold italic | bullist numlist | link | removeformat',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                branding: false,
            });
        }, 300);
    }
});

function addContentBlock() {
    const template = document.getElementById('content-block-template');
    const clone = template.content.cloneNode(true);
    
    document.getElementById('content-blocks-container').appendChild(clone);
    blockCount++;
}

function removeContentBlock(button) {
    button.closest('.content-block').remove();
}

function removeExistingContentBlock(button, index) {
    if (confirm('¿Estás segura de eliminar este bloque?')) {
        const block = button.closest('.content-block');
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_content_blocks[]';
        input.value = index;
        block.appendChild(input);
        
        block.style.display = 'none';
    }
}

// Drag & Drop para galería
function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('gallery-input');
    const preview = document.getElementById('gallery-preview');
    
    window.galleryFiles = [];
    
    dropZone.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.click();
    });
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName', preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.style.borderColor = '#3b82f6';
            dropZone.style.backgroundColor = '#eff6ff';
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.style.borderColor = '#d1d5db';
            dropZone.style.backgroundColor = '#f9fafb';
        }, false);
    });
    
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        handleFiles(files);
    }
    
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
    
    function handleFiles(files) {
        if (files.length === 0) {
            preview.innerHTML = '';
            preview.style.display = 'none';
            return;
        }
        
        preview.innerHTML = '';
        preview.style.display = 'grid';
        
        window.galleryFiles = Array.from(files);
        
        window.galleryFiles.forEach((file, index) => {
            renderFilePreview(file, index);
        });
    }
    
    function renderFilePreview(file, index) {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'relative border-2 border-gray-200 rounded-lg p-2';
            div.dataset.fileIndex = index;
            
            const isVideo = file.type.startsWith('video/');
            
            if (isVideo) {
                div.innerHTML = `
                    <video style="width: 100%; height: 150px; object-fit: contain; border-radius: 8px;" controls>
                        <source src="${e.target.result}" type="${file.type}">
                    </video>
                    <p style="color: #4b5563; font-size: 0.75rem; margin-top: 0.25rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</p>
                `;
            } else {
                div.innerHTML = `
                    <img src="${e.target.result}" 
                         style="width: 100%; height: 150px; object-fit: contain; border-radius: 8px;" 
                         alt="${file.name}">
                    <p style="color: #4b5563; font-size: 0.75rem; margin-top: 0.25rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</p>
                `;
            }
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.innerHTML = '×';
            removeBtn.style.cssText = `
                position: absolute;
                top: 0.25rem;
                right: 0.25rem;
                background-color: #dc2626;
                color: white;
                border-radius: 9999px;
                width: 1.75rem;
                height: 1.75rem;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.25rem;
                font-weight: bold;
                cursor: pointer;
                border: none;
            `;
            
            removeBtn.addEventListener('mouseover', function() {
                this.style.backgroundColor = '#b91c1c';
            });
            removeBtn.addEventListener('mouseout', function() {
                this.style.backgroundColor = '#dc2626';
            });
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                removeGalleryFile(index);
            });
            
            div.appendChild(removeBtn);
            preview.appendChild(div);
        };
        
        reader.readAsDataURL(file);
    }
    
    function removeGalleryFile(indexToRemove) {
        window.galleryFiles = window.galleryFiles.filter((file, index) => index !== indexToRemove);
        
        if (window.galleryFiles.length === 0) {
            fileInput.value = '';
            preview.innerHTML = '';
            preview.style.display = 'none';
            return;
        }
        
        const dt = new DataTransfer();
        window.galleryFiles.forEach(file => {
            dt.items.add(file);
        });
        fileInput.files = dt.files;
        
        preview.innerHTML = '';
        window.galleryFiles.forEach((file, newIndex) => {
            renderFilePreview(file, newIndex);
        });
    }
})();
</script>