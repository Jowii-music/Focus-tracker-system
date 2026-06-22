<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionEvent extends Model
{
    protected $table = 'session_events';

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'user_id',
        'type',
        'started_at',
        'ended_at',
        'duration_seconds',
        'created_at',
    ];
}