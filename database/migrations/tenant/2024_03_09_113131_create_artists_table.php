<?php

use App\Models\System\Country;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyText('about')->nullable();
            $table->foreignIdFor(Country::class);
            $table->string('ipi_code')->nullable();
            $table->string('isni_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->foreignIdFor(\App\Models\User::class, 'created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artists');
    }
};
