<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectCredential extends Model
{
    protected $table = 'project_credentials';

    protected $fillable = [
        'briefing_id',
        'environment',
        'service_name',
        'url',
        'username',
        'password',
        'notes'
    ];

    public function briefing()
    {
        return $this->belongsTo(ClientBriefing::class, 'briefing_id');
    }
}
