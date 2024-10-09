<?php

use App\Models\User;
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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('added_by')->after('add_new_to_streaming_platforms')->nullable()->constrained('users');
        });

        $products = DB::table('products')->get();

        foreach ($products as $product) {
            DB::table('products')->where('id', $product->id)->update(['added_by' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropColumn('added_by');
        });
    }
};
