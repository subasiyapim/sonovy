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
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->foreignId('added_by')->after('add_new_to_streaming_platforms')->nullable()->constrained('users');
        });

        $broadcasts = DB::table('broadcasts')->get();

        foreach ($broadcasts as $broadcast) {
            DB::table('broadcasts')->where('id', $broadcast->id)->update(['added_by' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broadcasts', function (Blueprint $table) {
            $table->dropForeign(['added_by']);
            $table->dropColumn('added_by');
        });
    }
};
