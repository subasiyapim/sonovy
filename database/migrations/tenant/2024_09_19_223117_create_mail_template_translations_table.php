<?php

use App\Models\MailTemplate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mail_template_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MailTemplate::class)->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('subject');
            $table->text('body');
            $table->unique(['mail_template_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_template_translations');
    }
};
