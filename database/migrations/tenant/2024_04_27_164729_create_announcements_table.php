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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['site', 'maintenance', 'email', 'sms'])->default('site');
            $table->text('content');
            $table->dateTime('from')->nullable()->comment('If this field and TO was empty it means SEND NOW');
            $table->dateTime('to')->nullable()->comment('If this field and FROM was empty it means SEND NOW');
            $table->enum('receivers', ['all', 'selected', 'all_but'])->default('all')
                ->comment('all: Will send to all users, selected: Just selected users will receive,
                all_exceptions: Will send to all users except the chosen users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
