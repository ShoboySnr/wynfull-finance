<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coach_availability_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
            $table->time('work_start_local');
            $table->time('work_end_local');
            $table->string('timezone', 64);
            $table->unsignedSmallInteger('session_duration_minutes');
            $table->timestamps();

            $table->unique('coach_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_availability_settings');
    }
};
