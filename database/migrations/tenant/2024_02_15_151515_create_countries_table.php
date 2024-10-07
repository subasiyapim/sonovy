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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso2');
            $table->string('iso3');
            $table->string('numeric_code');
            $table->string('phone_code');
            $table->string('capital');
            $table->string('currency');
            $table->string('currency_name');
            $table->string('currency_symbol');
            $table->string('tld');
            $table->string('native')->nullable();
            $table->string('region');
            $table->tinyInteger('region_id')->nullable();
            $table->string('subregion');
            $table->tinyInteger('subregion_id')->nullable();
            $table->string('nationality');
            $table->json('timezones');
            $table->json('translations');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('emoji');
            $table->string('emojiU');
            $table->boolean('is_active')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
