<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('order')->get();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'short_description' => 'required',
            'case_study' => 'nullable',
            'client' => 'nullable|max:255',
            'year' => 'nullable|max:4',
            'is_published' => 'boolean',
            'featured_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,mp4,webm|max:10240',
            'content_blocks_files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,mp4,webm|max:10240',
            'content_blocks_texts.*' => 'nullable|string',
            'gallery.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,mp4,webm|max:10240',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        
        // ✅ CAMBIO: Solo admin puede publicar, visitantes crean proyectos sin publicar
        if (auth()->user()->isAdmin()) {
            $validated['is_published'] = $request->has('is_published');
        } else {
            $validated['is_published'] = false;
        }
        
        // Procesar imagen principal
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('projects', 'public');
        }
        
        // Procesar bloques de contenido
        $contentBlocks = [];
        $files = $request->file('content_blocks_files') ?? [];
        $filesHover = $request->file('content_blocks_files_hover') ?? [];
        $texts = $request->input('content_blocks_texts') ?? [];

        foreach ($texts as $index => $text) {
            $block = [];
            
            // Guardar archivo principal si existe
            if (isset($files[$index]) && $files[$index]) {
                $block['file'] = $files[$index]->store('projects/content', 'public');
            }
            
            // Guardar archivo hover si existe
            if (isset($filesHover[$index]) && $filesHover[$index]) {
                $block['file_hover'] = $filesHover[$index]->store('projects/content', 'public');
            }
            
            // Guardar texto
            $block['text'] = $text;
            
            // Solo añadir el bloque si tiene contenido
            if (!empty($block['file']) || !empty($block['file_hover']) || !empty(trim($block['text']))) {
                $contentBlocks[] = $block;
            }
        }

        $validated['content_blocks'] = $contentBlocks;
        
        // Procesar galería
        if ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('projects/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        Project::create($validated);

        // ✅ CAMBIO: Mensaje diferente según el rol
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.projects.index')
                ->with('success', 'Proyecto creado exitosamente');
        } else {
            return redirect()->route('admin.projects.index')
                ->with('success', 'Proyecto creado. Un administrador lo revisará antes de publicarlo.');
        }
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        // ✅ VERIFICACIÓN: Solo admin puede actualizar
        if (auth()->user()->hasRole('visitante')) {
            return redirect()->back()
                ->with('error', '❌ No tienes permisos para editar contenidos. Solo los administradores pueden modificar proyectos.');
        }
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'short_description' => 'required',
            'case_study' => 'nullable',
            'client' => 'nullable|max:255',
            'year' => 'nullable|max:4',
            'featured_image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,mp4,webm|max:10240',
            'existing_blocks.*.new_file' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,mp4,webm|max:10240',
            'existing_blocks.*.text' => 'nullable|string',
            'new_content_blocks_files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,mp4,webm|max:10240',
            'new_content_blocks_texts.*' => 'nullable|string',
            'delete_content_blocks' => 'nullable|array',
            'delete_featured_image' => 'nullable',
            'delete_gallery' => 'nullable|array',
            'gallery.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,mp4,webm|max:10240',
        ]);

        // Actualizar campos básicos
        $project->title = $validated['title'];
        $project->slug = Str::slug($validated['title']);
        $project->short_description = $validated['short_description'];
        $project->case_study = $validated['case_study'];
        $project->client = $validated['client'];
        $project->year = $validated['year'];
        $project->is_published = $request->has('is_published');
        
        // Eliminar imagen principal si se marcó
        if ($request->has('delete_featured_image')) {
            if ($project->featured_image && Storage::disk('public')->exists($project->featured_image)) {
                Storage::disk('public')->delete($project->featured_image);
            }
            $project->featured_image = null;
        }
        
        // Procesar nueva imagen principal
        if ($request->hasFile('featured_image')) {
            if ($project->featured_image && Storage::disk('public')->exists($project->featured_image)) {
                Storage::disk('public')->delete($project->featured_image);
            }
            $project->featured_image = $request->file('featured_image')->store('projects', 'public');
        }
        
        // Procesar bloques de contenido
        $contentBlocks = [];
        $existingBlocks = $project->content_blocks ?? [];
        $blocksToDelete = $request->input('delete_content_blocks', []);
        
        // Procesar bloques existentes
        if ($request->has('existing_blocks')) {
            foreach ($request->input('existing_blocks') as $index => $blockData) {
                if (in_array($index, $blocksToDelete)) {
                    if (isset($existingBlocks[$index]['file'])) {
                        Storage::disk('public')->delete($existingBlocks[$index]['file']);
                    }
                    if (isset($existingBlocks[$index]['file_hover'])) {
                        Storage::disk('public')->delete($existingBlocks[$index]['file_hover']);
                    }
                    continue;
                }
                
                $block = [];
                
                // Archivo principal
                if ($request->hasFile("existing_blocks.$index.new_file")) {
                    if (isset($existingBlocks[$index]['file'])) {
                        Storage::disk('public')->delete($existingBlocks[$index]['file']);
                    }
                    $block['file'] = $request->file("existing_blocks.$index.new_file")->store('projects/content', 'public');
                } else {
                    if (isset($blockData['file'])) {
                        $block['file'] = $blockData['file'];
                    }
                }
                
                // Archivo hover
                if ($request->hasFile("existing_blocks.$index.new_file_hover")) {
                    if (isset($existingBlocks[$index]['file_hover'])) {
                        Storage::disk('public')->delete($existingBlocks[$index]['file_hover']);
                    }
                    $block['file_hover'] = $request->file("existing_blocks.$index.new_file_hover")->store('projects/content', 'public');
                } else {
                    if (isset($blockData['file_hover'])) {
                        $block['file_hover'] = $blockData['file_hover'];
                    }
                }
                
                $block['text'] = $blockData['text'] ?? '';
                
                if (!empty($block['file']) || !empty($block['file_hover']) || !empty(trim($block['text']))) {
                    $contentBlocks[] = $block;
                }
            }
        }

        // Procesar nuevos bloques
        $newFiles = $request->file('new_content_blocks_files') ?? [];
        $newFilesHover = $request->file('new_content_blocks_files_hover') ?? [];
        $newTexts = $request->input('new_content_blocks_texts') ?? [];

        foreach ($newTexts as $index => $text) {
            $block = [];
            
            if (isset($newFiles[$index]) && $newFiles[$index]) {
                $block['file'] = $newFiles[$index]->store('projects/content', 'public');
            }
            
            if (isset($newFilesHover[$index]) && $newFilesHover[$index]) {
                $block['file_hover'] = $newFilesHover[$index]->store('projects/content', 'public');
            }
            
            $block['text'] = $text;
            
            if (!empty($block['file']) || !empty($block['file_hover']) || !empty(trim($block['text']))) {
                $contentBlocks[] = $block;
            }
        }
        
        $project->content_blocks = $contentBlocks;
        
        // Procesar eliminaciones de galería
        if ($request->has('delete_gallery')) {
            $filesToDelete = $request->input('delete_gallery');
            $currentGallery = $project->gallery ?? [];
            
            foreach ($filesToDelete as $fileToDelete) {
                if (Storage::disk('public')->exists($fileToDelete)) {
                    Storage::disk('public')->delete($fileToDelete);
                }
                $currentGallery = array_values(array_filter($currentGallery, function($file) use ($fileToDelete) {
                    return $file !== $fileToDelete;
                }));
            }
            
            $project->gallery = $currentGallery;
        }
        
        // Procesar nuevas imágenes de galería
        if ($request->hasFile('gallery')) {
            $existingGallery = $project->gallery ?? [];
            $newFiles = [];
            
            foreach ($request->file('gallery') as $file) {
                $newFiles[] = $file->store('projects/gallery', 'public');
            }
            
            $project->gallery = array_merge($existingGallery, $newFiles);
        }
        
        $project->save();

        return redirect()->route('admin.projects.index')
            ->with('success', 'Proyecto actualizado exitosamente');
    }

    public function destroy(Project $project)
    {
        // ✅ VERIFICACIÓN: Solo admin puede eliminar
        if (auth()->user()->hasRole('visitante')) {
            return redirect()->back()
                ->with('error', '❌ No tienes permisos para eliminar contenidos. Solo los administradores pueden eliminar proyectos.');
        }
        
        $project->delete();
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Proyecto eliminado exitosamente');
    }

    public function deleteImage(Request $request, Project $project)
    {
        // ✅ VERIFICACIÓN: Solo admin puede eliminar imágenes
        if (auth()->user()->hasRole('visitante')) {
            return redirect()->back()
                ->with('error', '❌ No tienes permisos para eliminar contenidos. Solo los administradores pueden eliminar imágenes.');
        }
        
        $imageToDelete = $request->input('image');
        
        if ($request->input('type') === 'featured') {
            // Eliminar imagen principal
            if ($project->featured_image) {
                Storage::disk('public')->delete($project->featured_image);
                $project->featured_image = null;
                $project->save();
            }
        } else {
            // Eliminar imagen de la galería
            $gallery = $project->gallery ?? [];
            
            if (in_array($imageToDelete, $gallery)) {
                Storage::disk('public')->delete($imageToDelete);
                
                // Remover de la galería
                $gallery = array_values(array_filter($gallery, function($img) use ($imageToDelete) {
                    return $img !== $imageToDelete;
                }));
                
                $project->gallery = $gallery;
                $project->save();
            }
        }
        
        return back()->with('success', 'Imagen eliminada correctamente');
    }
}