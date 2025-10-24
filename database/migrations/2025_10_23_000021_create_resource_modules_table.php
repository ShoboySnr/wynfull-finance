<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resource_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_collection_id')->constrained('resource_collections')->cascadeOnDelete();

            $table->string('title', 200);
            $table->text('description')->nullable();

            // content meta
            $table->string('type')->default('file');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('video_link')->nullable();

            // workflow
            $table->enum('status', ['draft','pending','approved','rejected'])->default('draft')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // authorship
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['resource_collection_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_modules');
    }
};
