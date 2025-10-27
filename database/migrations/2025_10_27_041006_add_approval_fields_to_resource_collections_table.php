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
        Schema::table('resource_collections', function (Blueprint $table) {
            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->nullOnDelete()
                ->after('description');

            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('rejection_reason')->nullable()->after('approved_at');

            $table->index('approved_by');
            $table->index('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_collections', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropIndex(['approved_by']);
            $table->dropIndex(['approved_at']);

            $table->dropColumn(['approved_by', 'approved_at', 'rejection_reason']);
        });
    }
};
