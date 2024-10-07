<?php

use App\Models\EarningReportFile;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('earning_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EarningReportFile::class, 'earning_report_file_id')->constrained();
            $table->string('status')->default(1);
            $table->date('report_date');
            $table->date('sales_date');
            $table->string('platform');
            $table->string('country');
            $table->string('label_name');
            $table->string('artist_name');
            $table->string('release_name');
            $table->string('song_name');
            $table->string('upc_code');
            $table->string('isrc_code');
            $table->string('catalog_number')->nullable();
            $table->string('release_type')->nullable();
            $table->string('sales_type');
            $table->string('quantity');
            $table->string('currency');
            $table->decimal('unit_price', 20, 15)->nullable();
            $table->decimal('net_revenue', 20, 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earning_reports');
    }
};
