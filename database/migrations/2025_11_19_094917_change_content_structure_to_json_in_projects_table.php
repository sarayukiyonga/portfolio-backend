<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Eliminar los campos individuales
            $table->dropColumn([
                'content_image_1', 'content_text_1',
                'content_image_2', 'content_text_2',
                'content_image_3', 'content_text_3',
                'content_image_4', 'content_text_4',
                'content_image_5', 'content_text_5',
            ]);
            
            // Añadir nuevo campo JSON
            $table->json('content_blocks')->nullable()->after('case_study');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('content_blocks');
            
            // Restaurar campos individuales
            $table->string('content_image_1')->nullable();
            $table->text('content_text_1')->nullable();
            $table->string('content_image_2')->nullable();
            $table->text('content_text_2')->nullable();
            $table->string('content_image_3')->nullable();
            $table->text('content_text_3')->nullable();
            $table->string('content_image_4')->nullable();
            $table->text('content_text_4')->nullable();
            $table->string('content_image_5')->nullable();
            $table->text('content_text_5')->nullable();
        });
    }
};
