<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('earning_reports', 'period')) {
                $table->string('period')->nullable()->after('name');
            }

            if (!Schema::hasColumn('earning_reports', 'report_type')) {
                $table->string('report_type')->nullable()->after('period');
            }
            
            if (!Schema::hasColumn('earning_reports', 'file_size')) {
                $table->decimal('file_size', 8, 2)->nullable()->after('report_type');
            }

            if (!Schema::hasColumn('earning_reports', 'processed_at')) {
                $table->timestamp('processed_at')->nullable()->after('status');
            }

        });
    }

    public function down(): void
    {
        Schema::table('earning_reports', function (Blueprint $table) {
            $table->dropColumn([
                'report_type',
                'file_size',
                'processed_at',
                'period',
            ]);
        });
    }
};
