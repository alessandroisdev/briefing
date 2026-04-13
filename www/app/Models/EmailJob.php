<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailJob extends Model
{
    protected $table = 'email_jobs';
    
    protected $fillable = [
        'recipient_email',
        'recipient_name',
        'subject',
        'body',
        'status',
        'error_message',
        'attempts',
        'sent_at'
    ];
}
