<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('broadcast_download_platform', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Broadcast::class, 'broadcast_id')->constrained('broadcasts');
            $table->foreignIdFor(\App\Models\Platform::class, 'platform_id')->constrained('platforms');

            $table->integer('price')->nullable();
            $table->date('pre_order_date')->nullable();
            $table->date('platform_release_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_download_platform');
    }
};
