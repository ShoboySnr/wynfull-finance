<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('professional_title')->nullable();
            $table->json('specialities')->nullable(); // stored as JSON array
            $table->string('email')->nullable();      // profile contact email (can differ from users.email)
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamps();

            $table->index('last_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
