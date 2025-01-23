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
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('earning_report_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('report_date')->nullable();
            $table->string('reporting_month', 10)->nullable();
            $table->date('sales_date')->nullable();
            $table->string('sales_month', 10)->nullable();
            $table->unsignedBigInteger('platform_id')->nullable();
            $table->string('platform')->nullable();
            $table->string('streaming_type')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('country')->nullable();
            $table->string('region', 100)->nullable();
            $table->unsignedBigInteger('label_id')->nullable();
            $table->string('label_name')->nullable();
            $table->unsignedBigInteger('artist_id')->nullable();
            $table->string('artist_name')->nullable();
            $table->string('release_name')->nullable();
            $table->unsignedBigInteger('song_id')->nullable();
            $table->string('song_name')->nullable();
            $table->string('isrc_code')->nullable();
            $table->string('upc_code')->nullable();
            $table->string('catalog_number')->nullable();
            $table->string('client_payment_currency')->nullable();
            $table->decimal('quantity', 20, 9)->default(0);
            $table->decimal('mechanical_fee', 20, 9)->nullable();
            $table->decimal('gross_revenue', 20, 9)->nullable();
            $table->decimal('client_share_rate', 5, 2)->nullable();
            $table->decimal('client_share', 20, 9)->default(0);
            $table->string('streaming_subscription_type')->nullable();
            $table->string('release_type')->nullable();
            $table->string('sales_type')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('unit_price', 20, 9)->nullable();
            $table->decimal('earning', 20, 9)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
}; 