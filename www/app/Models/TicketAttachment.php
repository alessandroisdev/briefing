<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $table = 'ticket_attachments';

    public $timestamps = false; // created_at only

    protected $fillable = [
        'ticket_message_id',
        'file_name',
        'file_path',
        'file_type',
        'created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    public function message()
    {
        return $this->belongsTo(TicketMessage::class, 'ticket_message_id');
    }
}
