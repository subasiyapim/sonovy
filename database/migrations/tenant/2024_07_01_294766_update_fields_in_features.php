<?php

use App\Models\Artist;
use App\Models\PlanItem;
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

        Schema::table('features', function (Blueprint $table) {
            if (Schema::hasColumn('features', 'is_deletable'))
                $table->dropColumn('is_deletable');

            if (!Schema::hasColumn('features', 'plan_item_id'))
                $table->foreignIdFor(PlanItem::class, 'plan_item_id')->after('id')
                    ->constrained('plan_items')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {
            $table->boolean('is_deletable')->default(1)->after('amount');
            $table->dropForeign(['plan_item_id']);
            $table->dropColumn('plan_item_id');
        });
    }
};
