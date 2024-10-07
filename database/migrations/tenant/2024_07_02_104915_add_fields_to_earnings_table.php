<?php

use App\Models\Country;
use App\Models\Platform;
use App\Models\User;
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
            $table->foreignIdFor(\App\Models\EarningReport::class, 'report_id')->after('id')->constrained('earning_reports');
            $table->dateTime('report_date')->after('name');
            $table->string('country')->after('report_date')->nullable();
            $table->string('platform')->after('country')->nullable();
            $table->foreignIdFor(User::class, 'user_id')->after('platform')->nullable()->constrained();
            $table->foreignIdFor(User::class, 'uploaded_user_id')->after('user_id')->nullable()->constrained('users');
            $table->decimal('earning', 20, 15)->after('uploaded_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $table->dropForeign('earnings_report_id_foreign');
            $table->dropForeign('earnings_user_id_foreign');
            $table->dropForeign('earnings_uploaded_user_id_foreign');
            $table->dropColumn('report_date');
            $table->dropColumn('country');
            $table->dropColumn('platform');
            $table->dropColumn('user_id');
            $table->dropColumn('uploaded_user_id');
            $table->dropColumn('earning');
        });
    }
};
