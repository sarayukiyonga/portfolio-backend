<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Project;

// API para obtener todos los proyectos publicados
Route::get('/projects', function () {
    $projects = Project::where('is_published', true)
                      ->orderBy('order')
                      ->orderBy('created_at', 'desc')
                      ->get();
    
    return response()->json($projects);
});

// API para obtener un proyecto específico por slug
Route::get('/projects/{slug}', function ($slug) {
    $project = Project::where('slug', $slug)
                     ->where('is_published', true)
                     ->firstOrFail();
    
    return response()->json($project);
});