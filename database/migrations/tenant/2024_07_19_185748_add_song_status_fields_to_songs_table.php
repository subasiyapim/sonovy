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
        Schema::table('songs', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('added_by');
            $table->timestamp('status_changed_at')->nullable()->after('status');
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->nullOnDelete()->after('status_changed_at');
            $table->tinyText('note')->nullable()->after('status_changed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('status_changed_at');
            $table->dropForeign(['status_changed_by']);
            $table->dropColumn('status_changed_by');
            $table->dropColumn('note');
        });
    }
};
