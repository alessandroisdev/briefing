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
        'comments',
        'agreed_value'
    ];

    protected $casts = [
        'status' => \App\Enums\BriefingStatus::class,
        'form_data' => 'array',
        'agreed_value' => 'decimal:2'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function template()
    {
        return $this->belongsTo(BriefingTemplate::class, 'template_id');
    }

    public function messages()
    {
        return $this->hasMany(BriefingMessage::class, 'briefing_id');
    }

    public function credentials()
    {
        return $this->hasMany(ProjectCredential::class, 'briefing_id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'briefing_id');
    }
}
