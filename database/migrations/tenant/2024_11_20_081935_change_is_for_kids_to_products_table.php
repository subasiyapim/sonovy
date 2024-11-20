<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Product::all()->each(function (Product $product) {
            $product->update(['is_for_kids' => false]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_for_kids')->default(false)->change();
        });
    }
};
