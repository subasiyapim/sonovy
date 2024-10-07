<?php

use App\Models\Country;
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
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->comment('1: Sound, 2: Video');


            //Type 1: Sound
            $table->string('copyright_for_publication_image')->nullable();
            $table->foreignIdFor(Label::class, 'label_id')->nullable()->constrained('labels');
            $table->tinyText('right_to_perform_work')->nullable();


            //Type 2: Video
            $table->boolean('has_audiovisual_rights')->comment('görsel işitsel haklara sahibim')->default(0);
            $table->string('image_copyright')->comment('yayın kapağı / görseli telif hakkı')->nullable();
            $table->string('name')->comment('video catalog name')->nullable();
            $table->string('publisher_name')->nullable();
            $table->boolean('is_for_children')->default(0);
            $table->string('copyright_owner')->nullable();
            $table->string('isrc_code')->nullable();
            $table->tinyText('description')->nullable();
            $table->string('youtube_channel')->nullable();
            $table->string('youtube_channel_theme')->nullable();


            //partner
            $table->foreignIdFor(Genre::class, 'genre_id')->nullable()->constrained('genres');
            $table->foreignIdFor(Genre::class, 'sub_genre_id')->nullable()->constrained('genres');
            $table->boolean('is_compilation_publication')->default(0);
            $table->tinyInteger('stereo_id_type')->nullable();
            $table->string('upc_code')->nullable();
            $table->string('ean_code')->nullable();
            $table->string('jan_code')->nullable();
            $table->string('catalog_number')->nullable();
            $table->foreignIdFor(Country::class, 'language_id')->constrained('countries');

            $table->date('release_date')->comment('Yayının kayıt üretim yılı')->nullable();
            $table->date('original_release_date')->comment('Orjinal / fiziksel yayın tarihi (Opsiyonel)')->nullable();
            $table->date('remaster_release_date')->comment('Yayının düzenlendiği ve yeniden üretildiği');
            $table->boolean('has_been_released')->comment('Yayın/albüm daha önce yayınlandı mı')->default(0);
            $table->string('p_line');

            $table->tinyInteger('publish_country_type')->comment('Yayınlama hangi ülkelerde yapılsın?')->default(1);
            $table->boolean('is_publication_date_the_same')->default(1);
            $table->date('publication_date')->comment('Tüm platformlardaki yayın tarihi')->nullable();

            $table->boolean('add_new_to_streaming_platforms')->comment('Yeni yayın platformlarına otomatik ekle')->default(1);


            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};
