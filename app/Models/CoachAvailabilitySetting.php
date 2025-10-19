<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachAvailabilitySetting extends Model
{
    protected $fillable = [
        'coach_id',
        'work_start_local',
        'work_end_local',
        'timezone',
        'session_duration_minutes',
    ];

    protected $casts = [
        'work_start_local' => 'datetime:H:i',
        'work_end_local'   => 'datetime:H:i',
        'session_duration_minutes' => 'integer',
    ];

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}
