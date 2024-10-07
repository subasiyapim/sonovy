<?php

use App\Models\Language;
use App\Models\Timezone;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('title');
            $table->string('dns');
            $table->string('email');
            $table->string('copyright_email');
            $table->boolean('has_contact_form');
            $table->string('phone')->nullable();
            $table->foreignIdFor(User::class, 'user_id')->constrained('users');
            $table->string('language');
            $table->foreignIdFor(Timezone::class, 'timezone_id')->constrained('timezones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
