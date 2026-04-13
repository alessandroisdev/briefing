<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'user_id',
        'company_name',
        'address',
        'status',
        'pending_updates'
    ];

    protected $casts = [
        'pending_updates' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
