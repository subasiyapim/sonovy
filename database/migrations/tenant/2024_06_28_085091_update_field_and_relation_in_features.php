<?php

use App\Models\Artist;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('features', function (Blueprint $table) {

            $table->json('title')->after('id');
            $table->json('description')->after('title');
            $table->string('type')->after('description');
            $table->unsignedTinyInteger('limit')->nullable()->change();
            $table->boolean('is_deletable')->default(1)->after('amount');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('features', function (Blueprint $table) {

            $table->dropColumn('title');
            $table->dropColumn('description');
            $table->dropColumn('type');
            //$table->enum('period', [1, 2, 3])->default(1)->comment('1: OneTime, 2: Weekly, 3: Monthly');
            //$table->string('limit')->nullable();
            $table->dropColumn('is_deletable');
        });
    }
};
