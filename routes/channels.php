<?php

use App\Models\CoachClientAssignment;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.assignment.{assignmentId}', function ($user, $assignmentId) {
    $assigment = CoachClientAssignment::query()->find($assignmentId);
    if (! $assigment) return false;
    return (int)$user->id === (int)$assigment->coach_id || (int)$user->id === (int)$assigment->client_id;
});
