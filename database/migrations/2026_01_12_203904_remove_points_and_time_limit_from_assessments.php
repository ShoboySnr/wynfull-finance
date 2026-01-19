<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove time limit from resource_modules
        Schema::table('resource_modules', function (Blueprint $table) {
            $table->dropColumn('time_limit_minutes');
        });

        // Remove points from assessment_questions
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropColumn('points');
        });

        // Remove scoring fields from assessment_submissions
        Schema::table('assessment_submissions', function (Blueprint $table) {
            $table->dropColumn(['score', 'total_points', 'percentage']);
        });

        // Remove correctness tracking from assessment_answers
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->dropColumn(['is_correct', 'points_earned']);
        });

        // Remove is_correct from assessment_question_options
        Schema::table('assessment_question_options', function (Blueprint $table) {
            $table->dropColumn('is_correct');
        });

        // Drop assessment_progress table (no longer needed without time limits)
        Schema::dropIfExists('assessment_progress');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore time limit to resource_modules
        Schema::table('resource_modules', function (Blueprint $table) {
            $table->integer('time_limit_minutes')->nullable()->after('content');
        });

        // Restore points to assessment_questions
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->integer('points')->default(1)->after('question_type');
        });

        // Restore scoring fields to assessment_submissions
        Schema::table('assessment_submissions', function (Blueprint $table) {
            $table->integer('score')->nullable()->after('user_id');
            $table->integer('total_points')->nullable()->after('score');
            $table->decimal('percentage', 5, 2)->nullable()->after('total_points');
        });

        // Restore correctness tracking to assessment_answers
        Schema::table('assessment_answers', function (Blueprint $table) {
            $table->boolean('is_correct')->nullable()->after('answer_text');
            $table->integer('points_earned')->default(0)->after('is_correct');
        });

        // Restore is_correct to assessment_question_options
        Schema::table('assessment_question_options', function (Blueprint $table) {
            $table->boolean('is_correct')->default(false)->after('option_text');
        });

        // Recreate assessment_progress table
        Schema::create('assessment_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_module_id')->constrained()->onDelete('cascade');
            $table->json('answers')->nullable();
            $table->integer('time_remaining_seconds')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'resource_module_id']);
        });
    }
};
