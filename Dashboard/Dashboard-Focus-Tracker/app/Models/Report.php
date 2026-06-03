<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'userId',
        'report_date',
        'total_tracked_seconds',
        'productive_seconds',
        'distracted_seconds',
        'neutral_seconds',
        'focus_switches',
        'most_used_app',
        'most_used_site',
        'app_useage_json',
        'category_useage_json',
        'hourly_distribution_json',
        'session_stats_json',
    ];
}