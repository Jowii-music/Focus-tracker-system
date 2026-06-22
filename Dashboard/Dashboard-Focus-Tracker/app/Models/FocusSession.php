<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FocusSession extends Model
{
    protected $table = 'focus_sessions';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'goal',
        'status',
        'started_at',
        'ended_at',
        'target_seconds',
        'actual_seconds',
        'productive_seconds',
        'neutral_seconds',
        'distracted_seconds',
        'break_count',
        'break_seconds',
        'distraction_count',
        'too_distracted_count',
        'too_distracted_seconds',
        'result',
    ];
}