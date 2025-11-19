<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('content_image_1')->nullable()->after('featured_image');
            $table->text('content_text_1')->nullable()->after('content_image_1');
            
            $table->string('content_image_2')->nullable()->after('content_text_1');
            $table->text('content_text_2')->nullable()->after('content_image_2');
            
            $table->string('content_image_3')->nullable()->after('content_text_2');
            $table->text('content_text_3')->nullable()->after('content_image_3');
            
            $table->string('content_image_4')->nullable()->after('content_text_3');
            $table->text('content_text_4')->nullable()->after('content_image_4');
            
            $table->string('content_image_5')->nullable()->after('content_text_4');
            $table->text('content_text_5')->nullable()->after('content_image_5');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'content_image_1', 'content_text_1',
                'content_image_2', 'content_text_2',
                'content_image_3', 'content_text_3',
                'content_image_4', 'content_text_4',
                'content_image_5', 'content_text_5',
            ]);
        });
    }
};