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
            $table->foreignIdFor(\App\Models\EarningReport::class)->constrained();
            $table->foreignIdFor(\App\Models\User::class)->constrained();
            $table->string('name')->nullable();
            $table->date('report_date')->nullable();
            $table->date('sales_date')->nullable();
            $table->string('platform')->nullable();
            $table->foreignIdFor(\App\Models\Platform::class)->nullable()->constrained();
            $table->string('country')->nullable();
            $table->foreignIdFor(\App\Models\System\Country::class, 'country_id')->nullable();
            $table->string('label_name')->nullable();
            $table->foreignIdFor(\App\Models\Label::class, 'label_id')->nullable()->constrained();
            $table->string('artist_name')->nullable();
            $table->foreignIdFor(\App\Models\Artist::class, 'artist_id')->nullable()->constrained();
            $table->string('release_name')->nullable();
            $table->string('song_name')->nullable();
            $table->foreignIdFor(\App\Models\Song::class, 'song_id')->nullable()->constrained();
            $table->string('upc_code')->nullable();
            $table->string('isrc_code')->nullable();
            $table->string('catalog_number')->nullable();
            $table->string('release_type')->nullable();
            $table->string('sales_type')->nullable();
            $table->integer('quantity');
            $table->string('currency')->nullable();
            $table->decimal('unit_price', 20, 15)->nullable();
            $table->decimal('earning', 20, 15);
            $table->timestamps();
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
