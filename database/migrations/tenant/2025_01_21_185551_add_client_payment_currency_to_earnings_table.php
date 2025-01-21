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
        Schema::table('earnings', function (Blueprint $table) {
            if (!Schema::hasColumn('earnings', 'client_payment_currency')) {
                $table->string('client_payment_currency', 10)->nullable()->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            if (Schema::hasColumn('earnings', 'client_payment_currency')) {
                $table->dropColumn('client_payment_currency');
            }
        });
    }
};
