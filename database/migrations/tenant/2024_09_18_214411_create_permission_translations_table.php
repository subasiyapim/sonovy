<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale')->index();
            $table->foreignIdFor(Permission::class)->constrained()->onDelete('cascade');
            $table->string('name');
            $table->unique(['permission_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_translations');
    }
};
