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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'model_id'))
                $table->morphs('model');
            if (!Schema::hasColumn('orders', 'amount'))
                $table->string('amount')->after('model_type');
            if (!Schema::hasColumn('orders', 'user_id'))
                $table->foreignId('user_id')->after('amount')->constrained()->cascadeOnDelete();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['model_id']);
            $table->dropColumn('model_id');
            $table->dropColumn('model_type');
            $table->dropColumn('amount');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
