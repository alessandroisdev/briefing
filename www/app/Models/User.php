<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'document',
        'role',
        'password',
        'magic_link_token',
        'magic_link_expires'
    ];

    protected $hidden = [
        'password',
        'magic_link_token',
    ];

    protected $casts = [
        'role' => \App\Enums\UserRole::class,
        'magic_link_expires' => 'datetime',
    ];

    public function clientProfile()
    {
        return $this->hasOne(Client::class);
    }
}
