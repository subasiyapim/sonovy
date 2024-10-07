<?php

use App\Models\Broadcast;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('upcs', function (Blueprint $table) {
            $table->id();
            $table->string('upc')->unique();
            $table->foreignIdFor(Broadcast::class)->nullable()->constrained()->cascadeOnDelete();
            $table->date('use_by_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upcs');
    }
};
