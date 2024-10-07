<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('plans')->truncate();
        DB::table('plan_translations')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

};
