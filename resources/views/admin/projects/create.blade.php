<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nuevo Proyecto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                                Título del Proyecto *
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('title') border-red-500 @enderror"
                                   required>
                            @error('title')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="short_description">
                                Descripción Corta *
                            </label>
                            <textarea name="short_description" id="short_description" rows="3"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('short_description') border-red-500 @enderror"
                                      required>{{ old('short_description') }}</textarea>
                            @error('short_description')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        </div>

<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2" for="case_study">
        Caso de Estudio Completo
    </label>
    <textarea name="case_study" id="case_study" class="tinymce-full">{{ old('case_study') }}</textarea>
</div>

                        <!-- Imagen principal -->
<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2" for="featured_image">
        Imagen Principal
    </label>
    <input type="file" name="featured_image" id="featured_image" accept="image/*,video/mp4,video/webm"
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
    <p class="text-gray-600 text-xs italic mt-1">Formatos: JPG, PNG, WebP (máx. 2MB)</p>
</div>

<!-- Sección de Imágenes de Contenido -->
{{-- Sección de Bloques de Contenido Dinámicos --}}
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
        {{-- Los bloques se añadirán aquí dinámicamente --}}
    </div>
</div>

{{-- Template para nuevos bloques (oculto) --}}
<template id="content-block-template">
    <div class="content-block mb-4 p-4 bg-white rounded border-2 border-gray-300">
        <div class="flex justify-between items-center mb-3">
            <h4 class="font-semibold text-gray-700">Bloque de Contenido</h4>
            <button type="button" 
                    onclick="removeContentBlock(this)" 
                    style="background-color: #dc2626; color: white; font-weight: bold; padding: 0.25rem 0.75rem; border-radius: 0.375rem; font-size: 0.75rem;"
                    onmouseover="this.style.backgroundColor='#b91c1c'" 
                    onmouseout="this.style.backgroundColor='#dc2626'">
                🗑️ Eliminar
            </button>
        </div>
        
        {{-- Grid para las imágenes --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1rem;">
            {{-- Imagen Principal --}}
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Imagen/Video Principal
                </label>
                <input type="file" 
                       name="content_blocks_files[]" 
                       accept="image/*,video/mp4,video/webm"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-600 text-xs italic mt-1">Se muestra por defecto</p>
            </div>
            
            {{-- Imagen Hover --}}
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Imagen/Video al Hacer Hover
                </label>
                <input type="file" 
                       name="content_blocks_files_hover[]" 
                       accept="image/*,video/mp4,video/webm"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-600 text-xs italic mt-1">Se muestra al pasar el mouse</p>
            </div>
        </div>
        
        {{-- Texto (ocupa todo el ancho) --}}
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Texto
            </label>
            <textarea name="content_blocks_texts[]" 
                      rows="3"
                      placeholder="Describe este contenido..."
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>
    </div>
</template>

<script>
let blockCount = 0;
let tinyMceReady = false;

// Esperar a que TinyMCE esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar si TinyMCE está cargado
    if (typeof tinymce !== 'undefined') {
        tinyMceReady = true;
    }
    
    // Añadir un bloque inicial
    setTimeout(() => {
        addContentBlock();
    }, 500);
});

function addContentBlock() {
    const template = document.getElementById('content-block-template');
    const clone = template.content.cloneNode(true);
    
    // Añadir un ID único al textarea para TinyMCE
    const textarea = clone.querySelector('textarea');
    const uniqueId = 'content-text-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    textarea.id = uniqueId;
    
    document.getElementById('content-blocks-container').appendChild(clone);
    blockCount++;
    
    // Inicializar TinyMCE para el nuevo textarea
    if (tinyMceReady) {
        setTimeout(() => {
            tinymce.init({
                selector: '#' + uniqueId,
                height: 200,
                menubar: false,
                language: 'es',
                plugins: ['lists', 'link', 'autolink', 'code'],
                toolbar: 'undo redo | blocks | bold italic | bullist numlist | link | removeformat',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; line-height: 1.6; }',
                branding: false,
            });
        }, 100);
    }
}

function removeContentBlock(button) {
    const block = button.closest('.content-block');
    const textarea = block.querySelector('textarea');
    
    // Destruir instancia de TinyMCE antes de eliminar
    if (textarea && textarea.id && tinymce.get(textarea.id)) {
        tinymce.get(textarea.id).remove();
    }
    
    block.remove();
}
</script>

<!-- Galería de imágenes -->
 {{-- Galería con Drag & Drop --}}
<div class="mb-4">
    <label class="block text-gray-700 text-sm font-bold mb-2">
        Galería de Archivos
    </label>
    
    {{-- Input real de archivos (oculto pero funcional) --}}
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
    
    {{-- Preview de archivos seleccionados --}}
    <div id="gallery-preview" style="margin-top: 1rem; display: none; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;"></div>
</div>

<script>
// Drag & Drop functionality
(function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('gallery-input');
    const preview = document.getElementById('gallery-preview');
    
    // Variable global para guardar los archivos
    window.galleryFiles = [];
    
    // Click en la zona abre el selector de archivos
    dropZone.addEventListener('click', (e) => {
        e.preventDefault();
        fileInput.click();
    });
    
    // Prevenir comportamiento por defecto del navegador
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    // Highlight al arrastrar sobre la zona
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
    
    // Manejar el drop
    dropZone.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        // IMPORTANTE: Asignar archivos al input
        fileInput.files = files;
        handleFiles(files);
    }
    
    // Manejar selección normal de archivos
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
        
        // Guardar archivos globalmente
        window.galleryFiles = Array.from(files);
        
        // Renderizar cada archivo
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
                    <p class="text-xs text-gray-600 mt-1 truncate" style="color: #4b5563; font-size: 0.75rem; margin-top: 0.25rem;">${file.name}</p>
                `;
            } else {
                div.innerHTML = `
                    <img src="${e.target.result}" 
                         style="width: 100%; height: 150px; object-fit: contain; border-radius: 8px;" 
                         alt="${file.name}">
                    <p class="text-xs text-gray-600 mt-1 truncate" style="color: #4b5563; font-size: 0.75rem; margin-top: 0.25rem;">${file.name}</p>
                `;
            }
            
            // Crear botón de eliminar
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
        // Filtrar el archivo a eliminar
        window.galleryFiles = window.galleryFiles.filter((file, index) => index !== indexToRemove);
        
        // Si no quedan archivos, limpiar todo
        if (window.galleryFiles.length === 0) {
            fileInput.value = '';
            preview.innerHTML = '';
            preview.style.display = 'none';
            return;
        }
        
        // Actualizar el input con los archivos restantes
        const dt = new DataTransfer();
        window.galleryFiles.forEach(file => {
            dt.items.add(file);
        });
        fileInput.files = dt.files;
        
        // Re-renderizar la preview
        preview.innerHTML = '';
        window.galleryFiles.forEach((file, newIndex) => {
            renderFilePreview(file, newIndex);
        });
    }
})();
</script>
<!-- Final Galeria -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="client">
                                Cliente
                            </label>
                            <input type="text" name="client" id="client" value="{{ old('client') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="year">
                                Año
                            </label>
                            <input type="text" name="year" id="year" value="{{ old('year') }}"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                   placeholder="2024">
                        </div>
 @if(auth()->check() && auth()->user()->isAdmin())
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_published" value="1" 
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-600">Publicar proyecto</span>
                            </label>
                        </div>
@endif
                        <div class="flex items-center justify-between">
    <a href="{{ route('admin.projects.index') }}"
       class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
        Cancelar
    </a>
    <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
        Crear Proyecto
    </button>
</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
