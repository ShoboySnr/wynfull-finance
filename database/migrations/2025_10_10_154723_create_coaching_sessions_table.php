<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coaching_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            // Core schedule
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');

            // Metadata
            $table->string('title')->default('Coaching Session');
            $table->string('type', 60)->nullable();  // e.g., 'financial-review','intro','check-in'
            $table->string('location_url')->nullable(); // video link, meeting url, etc.
            $table->enum('status', ['scheduled','completed','cancelled'])->default('scheduled');
            $table->text('notes')->nullable();

            // audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['coach_id', 'starts_at']);
            $table->index(['client_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaching_sessions');
    }
};
