<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientBriefing extends Model
{
    protected $table = 'client_briefings';

    protected $fillable = [
        'client_id',
        'template_id',
        'title',
        'status',
        'form_data',
        'comments'
    ];

    protected $casts = [
        'status' => \App\Enums\BriefingStatus::class,
        'form_data' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function template()
    {
        return $this->belongsTo(BriefingTemplate::class, 'template_id');
    }
}
