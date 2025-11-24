<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            // who scheduled/hosts the meeting (admin for now)
            $table->foreignId('organizer_id')->constrained('users')->cascadeOnDelete();

            // who the meeting is with (coach or client)
            $table->foreignId('attendee_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            $table->string('status', 20)->default('scheduled'); // scheduled|completed|cancelled
            $table->string('mode', 20)->nullable();             // video|audio|in-person|call
            $table->string('meeting_link')->nullable();
            $table->string('audience')->default('single');
            $table->text('notes')->nullable();

            $table->foreignId('scheduled_by')->nullable()->constrained('users')->nullOnDelete(); // admin id (audit)
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->timestamps();

            $table->index(['organizer_id', 'starts_at']);
            $table->index(['attendee_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
