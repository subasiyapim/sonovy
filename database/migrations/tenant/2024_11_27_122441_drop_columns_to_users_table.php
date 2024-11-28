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
            $table->dropColumn('surname');
            $table->dropColumn('gender');
            $table->dropColumn('birth_date');
            $table->dropColumn('access_all_artists');
            $table->dropColumn('access_all_labels');
            $table->dropColumn('access_all_platforms');
            $table->dropColumn('company_name');
            $table->dropColumn('customer_number');
            $table->dropColumn('timezone_id');
            $table->dropColumn('last_login_at');
            $table->dropColumn('payment_email');
            $table->dropColumn('title');
            $table->dropColumn('subscribe_newsletter');
            $table->dropColumn('theme');
            $table->dropColumn('interface_language');
            $table->dropColumn('birth_place_id');
            $table->dropColumn('credit_cards');
            $table->dropColumn('bill_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('surname')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('access_all_artists')->default(false);
            $table->boolean('access_all_labels')->default(false);
            $table->boolean('access_all_platforms')->default(false);
            $table->string('company_name')->nullable();
            $table->string('customer_number')->nullable();
            $table->foreignId('timezone_id')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('payment_email')->nullable();
            $table->string('title')->nullable();
            $table->boolean('subscribe_newsletter')->default(false);
            $table->string('theme')->nullable();
            $table->string('interface_language')->nullable();
            $table->foreignId('birth_place_id')->nullable();
            $table->jsonb('credit_cards')->nullable();
            $table->jsonb('bill_info')->nullable();
        });
    }
};
