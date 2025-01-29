<?php

use App\Models\Label;
use App\Models\Platform;
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
        Schema::create('dsp_label', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignIdFor(Label::class, 'label_id');
            $table->foreignIdFor(Platform::class, 'platform_id');

            $table->enum('status', ['1', '2', '3'])
                ->default('1')
                ->comment('1: Pending, 2: Approved, 3: Canceled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dsp_label');
    }
};
