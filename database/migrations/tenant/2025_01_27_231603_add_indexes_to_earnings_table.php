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
        Schema::table('earnings', function (Blueprint $table) {
            // sales_date üzerinde index
            $table->index('sales_date', 'index_sales_date');

            // platform üzerinde index
            $table->index('platform', 'index_platform');

            // country üzerinde index
            $table->index('country', 'index_country');

            // user_id üzerinde filterleme için index
            $table->index('user_id', 'index_user_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            //
        });
    }
};
