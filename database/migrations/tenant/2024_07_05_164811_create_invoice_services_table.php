<?php

use App\Models\Invoice;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_services', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Invoice::class, 'invoice_id')->constrained();
            $table->foreignIdFor(Service::class, 'service_id')->constrained();
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->tinyInteger('tax_rate');
            $table->tinyInteger('tax_withholding_rate')->nullable();
            $table->string('tax_withholding_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_services');
    }
};
