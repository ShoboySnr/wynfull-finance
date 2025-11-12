<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = ['coach_client_assignment_id', 'sender_id', 'body', 'attachment_path', 'read_at'];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CoachClientAssignment::class, 'coach_client_assignment_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
