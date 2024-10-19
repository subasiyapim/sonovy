<?php

use App\Models\System\Country;
use App\Models\Language;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key and column if they exist
            if (Schema::hasColumn('users', 'place_of_birth_id')) {
                $table->dropColumn('place_of_birth_id');
            }

            // Add new foreign key column
            $table->foreignIdFor(Country::class, 'birth_place_id')
                ->nullable()
                ->after('is_verified');

            // Add other columns
            $table->string('company_name')->nullable()->after('is_verified');
            $table->string('customer_number')->nullable()->after('company_name');
            $table->string('payment_email')->nullable()->after('customer_number');
            $table->string('title')->nullable()->after('payment_email');
            $table->boolean('subscribe_newsletter')->default(true)->after('title');
            $table->string('theme')->default('light')->after('subscribe_newsletter');
            $table->string('interface_language')->nullable()->after('theme');

            // Add timezone foreign key
            $table->foreignIdFor(Country::class, 'timezone_id')
                ->nullable()
                ->after('interface_language');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('birth_place_id');

            // Drop other columns
            $table->dropColumn('company_name');
            $table->dropColumn('customer_number');
            $table->dropColumn('payment_email');
            $table->dropColumn('title');
            $table->dropColumn('subscribe_newsletter');
            $table->dropColumn('theme');

            // Drop 'timezone_id' foreign key and column if they exist
            if (Schema::hasColumn('users', 'timezone_id')) {
                $table->dropForeign(['timezone_id']);
                $table->dropColumn('timezone_id');
            }

            // Drop 'interface_language' column
            $table->dropColumn('interface_language');
        });
    }
};
