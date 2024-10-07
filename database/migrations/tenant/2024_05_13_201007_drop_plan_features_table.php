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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('plan_feature_translations');
        Schema::dropIfExists('plan_feature_plan');
        Schema::enableForeignKeyConstraints();
    }

};
