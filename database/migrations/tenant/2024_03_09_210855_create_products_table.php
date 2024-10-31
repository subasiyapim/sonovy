<?php

use App\Models\System\Country;
use App\Models\Genre;
use App\Models\Label;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->nullable()->comment('1: Sound, 2: Video');
            $table->string('album_name');

            //Type 1: Sound
            $table->string('version')->nullable();
            $table->boolean('mixed_album')->default(1);
            $table->foreignIdFor(Genre::class, 'genre_id')->nullable()->constrained('genres');
            $table->foreignIdFor(Genre::class, 'sub_genre_id')->nullable()->constrained('genres');
            $table->tinyInteger('format_id')->nullable();
            $table->foreignIdFor(Label::class, 'label_id')->nullable()->constrained('labels');
            $table->string('p_line')->nullable();
            $table->string('c_line')->nullable();
            $table->string('upc_code')->nullable();
            $table->string('ean_code')->nullable();
            $table->string('catalog_number')->nullable();
            $table->foreignIdFor(Country::class, 'language_id')->nullable();
            $table->decimal('main_price', 8, 2)->nullable();

            $table->foreignIdFor(\App\Models\User::class, 'created_by')->nullable()->constrained('users');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
