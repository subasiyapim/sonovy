<?php

use App\Models\Country;
use App\Models\Language;
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
            $table->dropForeign(['place_of_birth_id']);
            $table->dropColumn('place_of_birth_id');
            $table->foreignIdFor(Country::class, 'birth_place_id')->nullable()->after('is_verified')->constrained('countries');
            $table->string('company_name')->nullable()->after('is_verified');
            $table->string('customer_number')->nullable()->after('company_name');
            $table->string('payment_email')->nullable()->after('customer_number');
            $table->string('title')->nullable()->after('payment_email');
            $table->boolean('subscribe_newsletter')->default(1)->after('title');
            $table->string('theme')->default('light')->after('subscribe_newsletter');
            $table->string('interface_language')->nullable()->after('theme');
            $table->foreignIdFor(Country::class, 'timezone_id')->nullable()->after('interface_language')->constrained('countries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('place_of_birth_id')->nullable()->after('is_verified')->constrained('countries');
            $table->dropForeign(['birth_place_id']);
            $table->dropColumn('birth_place_id');
            $table->dropColumn('company_name');
            $table->dropColumn('customer_number');
            $table->dropColumn('payment_email');
            $table->dropColumn('title');
            $table->dropColumn('subscribe_newsletter');
            $table->dropColumn('theme');
            $table->dropForeign(['interface_language_id']);
            $table->dropColumn('interface_language');
            $table->dropForeign(['timezone_id']);
            $table->dropColumn('timezone_id');
        });
    }
};
