<?php

use App\Models\Plan;
use App\Models\PlanItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PlanItem::class, 'plan_item_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Plan::class, 'plan_id')->constrained()->cascadeOnDelete();
            $table->integer('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_plan');
    }
};
