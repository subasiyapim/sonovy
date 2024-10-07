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
        Schema::create('plan_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_feature_id')->constrained('plan_features')->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_feature_translations');
    }
};
