<?php

use App\Models\Announcement;
use App\Models\Artist;
use App\Models\ArtistBranch;
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
        Schema::table('announcement_user', function (Blueprint $table) {
            $table->enum('type', ['site', 'maintenance', 'email', 'sms']);
            $table->enum('status', ['NEW', 'DONE'])->default('NEW');
            $table->text('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcement_user', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('status');
            $table->dropColumn('content');
        });
    }
};
