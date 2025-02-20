<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            $table->string('period')->nullable()->after('name');
            $table->string('report_type')->nullable()->after('period');
            $table->decimal('file_size', 8, 2)->nullable()->after('report_type');
            $table->timestamp('processed_at')->nullable()->after('status');

            // Yeni kolonlar
            $table->decimal('gross_revenue', 20, 15)->nullable()->after('unit_price');
            $table->decimal('client_share_rate', 5, 2)->nullable()->after('gross_revenue');
            $table->decimal('mechanical_fee', 20, 15)->nullable()->after('unit_price');
            $table->string('region')->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            $table->dropColumn([
                'period',
                'report_type',
                'file_size',
                'processed_at',
                'streaming_subscription_type',
                'gross_revenue',
                'client_share_rate',
                'mechanical_fee',
                'region'
            ]);
        });
    }
};
