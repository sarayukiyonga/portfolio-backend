<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
        $table->string('title'); // Título del proyecto
        $table->string('slug')->unique(); // URL amigable
        $table->text('short_description'); // Descripción corta
        $table->longText('case_study')->nullable(); // Caso de estudio completo
        $table->string('featured_image')->nullable(); // Imagen principal
        $table->json('gallery')->nullable(); // Galería de imágenes (array)
        $table->string('client')->nullable(); // Cliente
        $table->string('year')->nullable(); // Año
        $table->json('tags')->nullable(); // Etiquetas/categorías
        $table->boolean('is_published')->default(false); // Publicado o no
        $table->integer('order')->default(0); // Orden de visualización
        $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
