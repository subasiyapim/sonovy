<?php

use App\Models\Artist;
use App\Models\PlanItem;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('expiration_date')->nullable()->after('plan');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->date('expiration_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->renameColumn('expiration_date', 'invoice_date');
        });
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('invoice_date')->nullable(false)->change();
        });
    }
};
