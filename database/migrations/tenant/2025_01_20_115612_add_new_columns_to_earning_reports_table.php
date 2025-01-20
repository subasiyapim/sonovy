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
        Schema::table('earning_reports', function (Blueprint $table) {
            // Raporlama ve satış ayı için
            $table->string('reporting_month', 10)->nullable()->after('report_date');
            $table->string('sales_month', 10)->nullable()->after('sales_date');
            
            // Bölge bilgisi için
            $table->string('region', 100)->nullable()->after('country');
            
            // Streaming bilgisi için
            $table->string('streaming_type', 50)->nullable()->after('catalog_number');
            
            // Para birimi ve ücret bilgileri
            $table->string('client_payment_currency', 10)->nullable()->after('currency')->default('EUR');
            $table->decimal('unit_price', 20, 12)->nullable()->change();
            $table->decimal('mechanical_fee', 20, 12)->nullable()->after('unit_price');
            $table->decimal('gross_revenue', 20, 12)->nullable()->after('mechanical_fee');
            $table->decimal('client_share_rate', 5, 2)->nullable()->after('gross_revenue');
            $table->decimal('net_revenue', 20, 12)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            $table->dropColumn([
                'mechanical_fee',
                'gross_revenue',
                'client_share_rate',
                'client_payment_currency',
                'reporting_month',
                'sales_month',
                'region',
                'streaming_type'
            ]);

            // Decimal alanları eski haline döndür
            $table->decimal('unit_price', 10, 2)->nullable()->change();
            $table->decimal('net_revenue', 10, 2)->nullable()->change();
        });
    }
};
