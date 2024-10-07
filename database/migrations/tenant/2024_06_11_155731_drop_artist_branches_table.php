<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //SQLSTATE[HY000]: General error: 3730 Cannot drop table 'artist_branches' referenced by a foreign key constraint 'artist_artist_branch_artist_branch_id_foreign' on table 'artist_artist_branch'. (Connection: mysql, SQL: drop table if exists `artist_branches`)
        Schema::table('artist_artist_branch', function (Blueprint $table) {
            $table->dropForeign('artist_artist_branch_artist_branch_id_foreign');
        });
        Schema::dropIfExists('artist_branches');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('artist_branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }
};
