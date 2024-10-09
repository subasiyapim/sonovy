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
        Schema::table('products', function (Blueprint $table) {
            $table->enum('status', ['0', '1', '2', '3', '4'])->after('add_new_to_streaming_platforms')
                ->default(0)
                ->comment('0: NEW, 1: WAITING_FOR_APPROVAL, 2: APPROVED, 3: REJECTED, 4: NOT_BROADCASTING');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
