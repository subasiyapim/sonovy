<?php

use App\Models\Broadcast;
use App\Models\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('broadcast_published_country', function (Blueprint $table) {
            $table->foreignIdFor(Broadcast::class, 'broadcast_id')->constrained('broadcasts');
            $table->foreignIdFor(Country::class, 'country_id')->constrained('countries');
            $table->unique(['broadcast_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast_published_country');
    }
};
