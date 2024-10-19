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


            //Type 1: Sound
            $table->string('copyright_for_publication_image')->nullable();
            $table->foreignIdFor(Label::class, 'label_id')->nullable()->constrained('labels');
            $table->tinyText('right_to_perform_work')->nullable();


            //Type 2: Video
            $table->boolean('has_audiovisual_rights')->nullable()->comment('görsel işitsel haklara sahibim');
            $table->string('image_copyright')->nullable()->comment('yayın kapağı / görseli telif hakkı')->nullable();
            $table->string('name')->nullable()->comment('video catalog name');
            $table->string('publisher_name')->nullable();
            $table->boolean('is_for_children')->nullable();
            $table->string('copyright_owner')->nullable();
            $table->string('isrc_code')->nullable();
            $table->tinyText('description')->nullable();
            $table->string('youtube_channel')->nullable();
            $table->string('youtube_channel_theme')->nullable();


            //partner
            $table->foreignIdFor(Genre::class, 'genre_id')->nullable()->constrained('genres');
            $table->foreignIdFor(Genre::class, 'sub_genre_id')->nullable()->constrained('genres');
            $table->boolean('is_compilation_publication')->nullable();
            $table->tinyInteger('stereo_id_type')->nullable();
            $table->string('upc_code')->nullable();
            $table->string('ean_code')->nullable();
            $table->string('jan_code')->nullable();
            $table->string('catalog_number')->nullable();
            $table->foreignIdFor(Country::class, 'language_id')->nullable();

            $table->date('release_date')->nullable()->comment('Yayının kayıt üretim yılı');
            $table->date('original_release_date')->nullable()->comment('Orjinal / fiziksel yayın tarihi (Opsiyonel)');
            $table->date('remaster_release_date')->nullable()->comment('Yayının düzenlendiği ve yeniden üretildiği');
            $table->boolean('has_been_released')->nullable()->comment('Yayın/albüm daha önce yayınlandı mı');
            $table->string('p_line');

            $table->tinyInteger('publish_country_type')->nullable()->comment('Yayınlama hangi ülkelerde yapılsın?');
            $table->boolean('is_publication_date_the_same')->nullable();
            $table->date('publication_date')->nullable()->comment('Tüm platformlardaki yayın tarihi');

            $table->boolean('add_new_to_streaming_platforms')->nullable()->comment('Yeni yayın platformlarına otomatik ekle');


            $table->timestamps();
            $table->softDeletes();
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
