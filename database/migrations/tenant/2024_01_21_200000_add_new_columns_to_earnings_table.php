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
            // Raporlama ve satış ayı için
            if (!Schema::hasColumn('earnings', 'reporting_month')) {
                $table->string('reporting_month', 10)->nullable()->after('report_date');
            }
            if (!Schema::hasColumn('earnings', 'sales_month')) {
                $table->string('sales_month', 10)->nullable()->after('sales_date');
            }
            
            // Para birimi ve ücret bilgileri
            if (!Schema::hasColumn('earnings', 'client_payment_currency')) {
                $table->string('client_payment_currency', 10)->nullable()->after('currency')->default('EUR');
            }
            if (!Schema::hasColumn('earnings', 'mechanical_fee')) {
                $table->decimal('mechanical_fee', 20, 12)->nullable()->after('unit_price');
            }
            if (!Schema::hasColumn('earnings', 'gross_revenue')) {
                $table->decimal('gross_revenue', 20, 12)->nullable()->after('mechanical_fee');
            }
            if (!Schema::hasColumn('earnings', 'client_share_rate')) {
                $table->decimal('client_share_rate', 5, 2)->nullable()->after('gross_revenue');
            }

            // Earning Report ID'yi nullable yap
            if (Schema::hasColumn('earnings', 'earning_report_id')) {
                $table->foreignIdFor(\App\Models\EarningReport::class)->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earnings', function (Blueprint $table) {
            $columns = [
                'reporting_month',
                'sales_month',
                'client_payment_currency',
                'mechanical_fee',
                'gross_revenue',
                'client_share_rate'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('earnings', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('earnings', 'earning_report_id')) {
                $table->foreignIdFor(\App\Models\EarningReport::class)->nullable(false)->change();
            }
        });
    }
}; 