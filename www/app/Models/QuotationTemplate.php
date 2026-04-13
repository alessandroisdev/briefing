<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationTemplate extends Model
{
    protected $table = 'quotation_templates';

    protected $fillable = [
        'title',
        'description',
        'base_items_json',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'base_items_json' => 'array'
    ];
}
