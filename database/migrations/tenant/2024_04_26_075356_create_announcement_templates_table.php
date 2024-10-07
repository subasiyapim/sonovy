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
        Schema::create('announcement_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['site', 'maintenance', 'email', 'sms'])->default('site');
            $table->enum('send_type', ['automatic', 'manual'])->default('automatic');
            $table->text('content');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_templates');
    }
};
