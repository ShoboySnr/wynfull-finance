<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resource_module_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_module_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'resource_module_id']);
            $table->index(['user_id', 'completed_at']);
            $table->index(['resource_module_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_module_users');
    }
};
