<?php

use App\Models\BankAccount;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->tinyInteger('process_type')->default(1)->comment('1: Deposit, 2: Withdraw');
            $table->tinyInteger('status')->comment('1: Pending, 2: Approved, 3: Rejected');
            $table->decimal('amount', 10, 2);
            $table->foreignIdFor(BankAccount::class, 'account_id')->nullable()->constrained('bank_accounts');
            $table->dateTime('payment_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
