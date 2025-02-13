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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('payment_threshold', 10, 2)->default(0)->after('commission_rate');
            $table->string('currency')->nullable();
            $table->string('last_login_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('payment_threshold');
            $table->dropColumn('currency');
            $table->dropColumn('last_login_at');
        });
    }
};
