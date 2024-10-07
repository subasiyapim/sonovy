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
            $table->tinyInteger('gender')->nullable()->after('email_verified_at');
            $table->foreignId('place_of_birth_id')->nullable()->after('gender')->constrained('cities')->cascadeOnDelete();
            $table->date('birth_date')->nullable()->after('place_of_birth_id');
            $table->boolean('access_all_artists')->default(true)->after('birth_date');
            $table->boolean('access_all_labels')->default(true)->after('access_all_artists');
            $table->boolean('access_all_platforms')->default(true)->after('access_all_labels');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gender');
            $table->dropForeign(['place_of_birth_id']);
            $table->dropColumn('place_of_birth_id');
            $table->dropColumn('birth_date');
            $table->dropColumn('access_all_artists');
            $table->dropColumn('access_all_labels');
            $table->dropColumn('access_all_platforms');
        });
    }
};
