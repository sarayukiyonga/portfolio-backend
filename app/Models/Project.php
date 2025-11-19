<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
    'title',
    'slug',
    'short_description',
    'case_study',
    'featured_image',
    'content_blocks', 
    'gallery',
    'client',
    'year',
    'tags',
    'is_published',
    'order',
];

    protected $casts = [
        'gallery' => 'array',
        'content_blocks' => 'array',
        'tags' => 'array',
        'is_published' => 'boolean',
    ];
}
