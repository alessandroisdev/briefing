<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BriefingTemplate extends Model
{
    protected $table = 'briefing_templates';

    protected $fillable = [
        'title',
        'description',
        'form_schema',
        'status',
    ];

    protected $casts = [
        'form_schema' => 'array',
    ];
}
