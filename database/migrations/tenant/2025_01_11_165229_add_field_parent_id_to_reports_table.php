<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('batch_id');
            $table->foreignIdFor(\App\Models\Report::class, 'parent_id')->nullable()->constrained('reports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->uuid('batch_id')->nullable();
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
