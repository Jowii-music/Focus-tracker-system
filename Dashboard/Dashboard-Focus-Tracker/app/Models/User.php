<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'userId';

    public $timestamps = true;

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];
}