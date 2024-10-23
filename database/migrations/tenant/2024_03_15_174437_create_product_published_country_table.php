<?php

use App\Models\Product;
use App\Models\System\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_published_country', function (Blueprint $table) {
            $table->foreignIdFor(Product::class, 'product_id')->constrained('products');
            $table->foreignIdFor(Country::class, 'country_id');
            $table->unique(['product_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_published_country');
    }
};
