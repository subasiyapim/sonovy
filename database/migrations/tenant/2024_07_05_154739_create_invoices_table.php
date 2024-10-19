<?php

use App\Models\System\City;
use App\Models\System\Country;
use App\Models\System\District;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('invoice_type');
            $table->string('invoice_number');
            $table->string('name');
            $table->date('invoice_date');
            $table->time('invoice_time');
            $table->string('tax_office')->nullable();
            $table->integer('tax_number')->nullable();
            $table->string('commercial_register_number')->nullable();
            $table->foreignIdFor(Country::class, 'country_id');
            $table->foreignIdFor(District::class, 'district_id');
            $table->foreignIdFor(City::class, 'city_id');
            $table->string('zip_code');
            $table->string('address');
            $table->tinyText('note')->nullable();
            $table->string('phone_code');
            $table->string('phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
