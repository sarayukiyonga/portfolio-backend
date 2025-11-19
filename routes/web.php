<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
    Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('projects', ProjectController::class);
    Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('projects', ProjectController::class);
    //Route::post('projects/{project}/delete-image', [ProjectController::class, 'deleteImage'])->name('projects.deleteImage');
    });
});

// Rutas de administración (Solo Admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard de admin
    Route::get('/dashboard', function () {
        $usersCount = \App\Models\User::count();
        $adminsCount = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();
        $rolesCount = \App\Models\Role::count();
        
        return view('admin.dashboard', compact('usersCount', 'adminsCount', 'rolesCount'));
    })->name('dashboard');
    
    // Gestión de Usuarios
    Route::resource('users', UserManagementController::class)->except(['show']);
    Route::patch('/users/{user}/toggle-admin', [UserManagementController::class, 'toggleAdmin'])
        ->name('users.toggle-admin');
    
    // Gestión de Roles
    Route::resource('roles', RoleController::class)->except(['show']);
});

require __DIR__.'/auth.php';
