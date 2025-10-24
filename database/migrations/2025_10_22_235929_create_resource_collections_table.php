<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resource_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete(); // owner (must have role:coach)
            $table->string('icon_class', 100)->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['coach_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_collections');
    }
};
