<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resource_module_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // the client
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete(); // admin
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique(['resource_module_id', 'user_id']); // no duplicate assignment
            $table->index(['user_id', 'assigned_at']);
            $table->index(['resource_module_id', 'assigned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_module_assignments');
    }
};
