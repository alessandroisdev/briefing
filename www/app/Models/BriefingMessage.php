<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BriefingMessage extends Model
{
    protected $table = 'client_briefing_messages';

    protected $fillable = [
        'briefing_id',
        'sender_id',
        'message',
        'is_internal'
    ];

    protected $casts = [
        'is_internal' => 'boolean'
    ];

    public function briefing()
    {
        return $this->belongsTo(ClientBriefing::class, 'briefing_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
