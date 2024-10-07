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
            $table->foreignIdFor(\App\Models\User::class, 'parent_id')->nullable()->after('id');
            $table->integer('commission_rate')->nullable()->after('credit_cards');
            $table->foreignIdFor(\App\Models\State::class, 'state_id')->nullable()->after('country_id');
            $table->foreignIdFor(\App\Models\City::class, 'city_id')->nullable()->after('state_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('parent_id');
            $table->dropColumn('commission_rate');
            $table->dropColumn('state_id');
            $table->dropColumn('city_id');
        });
    }
};
