<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coach_client_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            $table->boolean('is_primary')->default(true);
            $table->enum('status', ['active', 'ended', 'pending'])->default('active');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('ended_at')->nullable();

            // audit
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();

            // subscription linkage (future)
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            //  - Only one row per (client_id, is_primary=1) via unique
            //  - When “multi-coach” arrives, set is_primary=0 for additional coaches.
            $table->unique(['client_id', 'is_primary']);

            // frequently queried pairs
            $table->index(['coach_id', 'client_id']);
            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_client_assignments');
    }
};
