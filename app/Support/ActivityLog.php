<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLog
{
    public static function log(
        string $log,
        string $event,
        string $message,
        Request $request,
        ?Model $subject = null,
        ?Model $causer = null,
        array $extra = []
    ): void {
        $user = $request->user();
        activity()
            ->useLog($log)
            ->when($subject, fn($a) => $a->performedOn($subject))
            ->when($causer ?? $user, fn($a, $c) => $a->causedBy($c))
            ->event($event)
            ->withProperties(array_filter([
                    'ip'         => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                ]) + $extra)
            ->log($message);
    }
}
