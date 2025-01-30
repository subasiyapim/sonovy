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
        if (Schema::hasTable('product_download_platform')) {
            $data = DB::table('product_download_platform')->get();

            if ($data->count() > 0) {
                foreach ($data as $item) {
                    DB::table('product_download_platform')
                        ->where('id', $item->id)
                        ->update(['status' => rand(1, 6)]);
                }
            }
        }

        if (Schema::hasTable('product_platform_history')) {
            $data = DB::table('product_platform_history')->get();

            if ($data->count() > 0) {
                foreach ($data as $item) {
                    DB::table('product_platform_history')
                        ->where('id', $item->id)
                        ->update(['status' => rand(1, 6)]);
                }
            }
        }
    }
};
