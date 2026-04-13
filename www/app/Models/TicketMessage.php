<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMessage extends Model
{
    protected $table = 'ticket_messages';

    public $timestamps = false; // We only have created_at in the migration

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
        'created_at'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'created_at' => 'datetime'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }
}
