<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coach_client_assignments', function (Blueprint $table) {
            $table->foreignId('coach_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // who performed the assignment (admin or privileged user)
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('assigned_at')->useCurrent();
            $table->string('status', 20)->default('active'); // active|ended (future-proof)
            $table->timestamps();

            // prevent duplicates at DB level
            $table->unique(['coach_id', 'client_id']);

            $table->index('coach_id');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_client_assignments');
    }
};
