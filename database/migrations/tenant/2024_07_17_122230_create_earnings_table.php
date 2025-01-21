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
        if (!Schema::hasTable('earnings')) {
            Schema::create('earnings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('earning_report_id')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->string('name')->nullable();
                $table->date('report_date')->nullable();
                $table->string('reporting_month', 10)->nullable();
                $table->date('sales_date')->nullable();
                $table->string('sales_month', 10)->nullable();
                $table->string('platform')->nullable();
                $table->unsignedBigInteger('platform_id')->nullable();
                $table->string('country')->nullable();
                $table->string('region', 100)->nullable();
                $table->unsignedBigInteger('country_id')->nullable();
                $table->string('label_name')->nullable();
                $table->unsignedBigInteger('label_id')->nullable();
                $table->string('artist_name')->nullable();
                $table->unsignedBigInteger('artist_id')->nullable();
                $table->string('release_name')->nullable();
                $table->string('song_name')->nullable();
                $table->unsignedBigInteger('song_id')->nullable();
                $table->string('upc_code')->nullable();
                $table->string('isrc_code')->nullable();
                $table->string('catalog_number')->nullable();
                $table->string('release_type')->nullable();
                $table->string('sales_type')->nullable();
                $table->integer('quantity');
                $table->string('currency')->nullable();
                $table->string('client_payment_currency', 10)->nullable()->default('EUR');
                $table->decimal('unit_price', 20, 15)->nullable();
                $table->decimal('mechanical_fee', 20, 12)->nullable();
                $table->decimal('gross_revenue', 20, 12)->nullable();
                $table->decimal('client_share_rate', 5, 2)->nullable();
                $table->decimal('earning', 20, 15);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
}; 