<?php

namespace App\Services;

use App\Models\ResourceModule;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ModuleCompletionService
{
    public function complete(User $user, ResourceModule $module): void
    {
        DB::transaction(function () use ($user, $module) {
            $user->moduleCompletions()->syncWithoutDetaching([
                $module->id => ['completed_at' => now()],
            ]);
        });
    }

    public function undo(User $user, ResourceModule $module): void
    {
        DB::transaction(function () use ($user, $module) {
            $user->moduleCompletions()->detach($module->id);
        });
    }
}
