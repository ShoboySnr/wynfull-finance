<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('resource_modules', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('resource_collection_id');

            $table->index(['resource_collection_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('resource_modules', function (Blueprint $table) {
            $table->dropIndex(['resource_collection_id', 'sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
