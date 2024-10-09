<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * @property mixed barcode_type
 * @property mixed $type
 */
class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('product_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match (intval($this->type)) {

            ProductTypeEnum::SOUND->value, ProductTypeEnum::RINGTONE->value => $this->soundRules(),
            ProductTypeEnum::VIDEO->value => $this->videoRules(),
            default => [],
        };
    }

    protected function commonRules(): array
    {
        $validated_data = [
            'status' => ['required', 'in:0,5'],
            'type' => [
                'required',
                'in:'.ProductTypeEnum::SOUND->value.','.ProductTypeEnum::VIDEO->value.','.ProductTypeEnum::RINGTONE->value
            ],
            'version' => ['nullable'],
            'label_id' => ['required', 'exists:labels,id'],
            'image_copyright' => ['nullable'],
            'publisher_name' => ['nullable'],
            'name' => ['required', 'string', 'max:255'],
            'p_line' => ['required', 'string'],
            'image' => ['required', 'mimes:jpeg,jpg,png', 'max:10000'],
            'genre_id' => ['required', 'exists:genres,id'],
            'sub_genre_id' => ['nullable', 'exists:genres,id'],
            'catalog_number' => ['nullable', 'string'],
            'language_id' => ['required', 'exists:languages,id'],
            'release_date' => ['required', 'date'],
            'original_release_date' => ['nullable', 'date'],
            'has_been_released' => ['required', 'boolean'],
            'publish_country_type' => ['required', 'in:1,2,3'],
            'is_publication_date_the_same' => ['required', 'boolean'],
            'publication_date' => ['required', 'date'],
            'add_new_to_streaming_platforms' => ['required', 'boolean'],
            'album_type' => ['required', 'in:1,2,3'],

            'songs' => ['required', 'array'],
            'songs.*.id' => ['required', 'exists:songs,id'],
            'songs.*.name' => ['required', 'string'],
            'songs.*.genre_id' => ['required', 'exists:genres,id'],
            'songs.*.sub_genre_id' => ['nullable', 'exists:genres,id'],
            'songs.*.isrc' => ['nullable', 'unique:songs,isrc'],
            'songs.*.is_instrumental' => ['required', 'boolean'],
            'songs.*.is_explicit' => ['required', 'boolean'],
            'songs.*.language_id' => ['required', 'exists:countries,id'],
            'songs.*.lyrics' => ['nullable', 'string'],
            'songs.*.lyrics_writers' => ['nullable', 'array'],
            'songs.*.lyrics_writers.*.id' => ['exists:users,id'],
            'songs.*.lyrics_writers.*.tasks' => ['required'],
            'songs.*.lyrics_writers.*.rate' => ['required', 'numeric'],
            'songs.*.iswc' => ['nullable', 'string', 'unique:songs,iswc'],
            'songs.*.preview_start' => ['nullable', 'string'],
            'songs.*.is_cover' => ['required', 'boolean'],
            'songs.*.remixer_artis' => ['nullable', 'exists:artists,id'],
            'songs.*.released_before' => ['required', 'boolean'],
            'songs.*.remaster_release_date' => ['nullable', 'date'],
            'songs.*.original_release_date' => ['nullable', 'date'],

            'platforms' => ['nullable', 'array'],
            'platforms.*.platformId' => ['required', 'exists:platforms,id'],
            'platforms.*.platform_pre_order_date' => ['nullable', 'date'],
            'platforms.*.platform_price' => ['nullable'],
            'platforms.*.publish_date' => ['required', 'date'],

            'promotion_info' => ['nullable', 'array'],
            'promotion_info.*.platform_id' => ['required', 'exists:platforms,id'],
            'promotion_info.*.promotion_date' => ['required', 'date'],
            'promotion_info.*.promotion_text' => ['required', 'string'],
            'promotion_info.*.title' => ['required', 'string'],

            'main_artists' => ['required', 'exists:artists,id'],
            'featuring_artists' => ['nullable', 'exists:artists,id'],
        ];

        $barcodeTypes = ['upc_code' => 1, 'ean_code' => 2, 'jan_code' => 3];
        foreach ($barcodeTypes as $code => $type) {
            if ($this->barcode_type == $type && $type != 1) {
                $validated_data[$code] = ['required', 'string'];
                break;
            }
        }

        if ($validated_data['has_been_released'] == 1) {
            $validated_data['remaster_release_date'] = ['required', 'date'];
        }

        return $validated_data;

    }

    private function soundRules(): array
    {
        return array_merge($this->commonRules(), [
            'right_to_perform_work' => ['required', 'string'],
            'is_compilation_publication' => ['required', 'boolean'],
        ]);

    }

    private function videoRules(): array
    {
        //dd($this->all());
        return array_merge($this->commonRules(), [
            'has_audiovisual_rights' => ['required', 'boolean'],
            'is_for_children' => ['required', 'boolean'],
            'copyright_owner' => ['required'],
            'youtube_channel' => ['nullable'],
            'youtube_channel_theme' => ['nullable'],
            'description' => ['required'],
            'hashtags' => ['required', 'array'],
            'hashtags.*' => ['required', 'string'],
            'copyright_for_publication_image' => ['required_if:has_audiovisual_rights,0'],
        ]);
    }

    public function attributes(): array
    {
        return [
            'type' => __('control.product.form.type'),
            'label_id' => __('control.product.label_company'),
            'right_to_perform_work' => __('control.product.form.right_to_perform_work'),
            'has_audiovisual_rights' => __('control.product.form.has_audiovisual_rights'),
            'copyright_for_publication_image' => __('control.product.form.copyright_for_publication_image'),
            'publisher_name' => __('control.product.form.publisher_name'),
            'is_for_children' => __('control.product.form.is_for_children'),
            'youtube_channel' => __('control.product.form.youtube_channel'),
            'youtube_channel_theme' => __('control.product.form.youtube_channel_theme'),
            'description' => __('control.product.form.description'),
            'hashtags' => __('control.product.form.hashtags'),
            'hashtags.*' => __('control.product.form.hashtags.*'),
            'name' => __('control.product.form.name'),
            'p_line' => __('control.product.form.p_line'),
            'image' => __('control.product.form.image'),
            'genre_id' => __('control.product.form.genre'),
            'sub_genre_id' => __('control.product.form.sub_genre'),
            'is_compilation_publication' => __('control.product.form.is_compilation_publication'),
            'catalog_number' => __('control.product.form.catalog_number'),
            'language_id' => __('control.product.form.language'),
            'release_date' => __('control.product.form.release_date'),
            'original_release_date' => __('control.product.form.original_release_date'),
            'remaster_release_date' => __('control.product.form.remaster_release_date'),
            'has_been_released' => __('control.product.form.has_been_released'),
            'publish_country_type' => __('control.product.form.publish_country_type'),
            'is_publication_date_the_same' => __('control.product.form.is_publication_date_the_same'),
            'publication_date' => __('control.product.form.publication_date'),
            'add_new_to_streaming_platforms' => __('control.product.form.add_new_to_streaming_platforms'),
            'songs' => __('control.product.form.songs'),
            'songs.*.name' => __('control.product.form.name'),
            'songs.*.genre_id' => __('control.product.form.genre'),
            'songs.*.sub_genre_id' => __('control.product.form.sub_genre'),
            'songs.*.isrc' => __('control.product.form.isrc'),
            'songs.*.is_instrumental' => __('control.product.form.is_instrumental'),
            'songs.*.is_explicit' => __('control.product.form.is_explicit'),
            'songs.*.language_id' => __('control.product.form.language'),
            'songs.*.lyrics' => __('control.product.form.lyrics'),
            'songs.*.lyrics_writers' => __('control.product.form.lyrics_writers'),
            'songs.*.lyrics_writers.*.id' => __('control.product.form.lyrics_writers.*.id'),
            'songs.*.lyrics_writers.*.tasks' => __('control.product.form.lyrics_writers.*.tasks'),
            'songs.*.lyrics_writers.*.rate' => __('control.product.form.lyrics_writers.*.rate'),
            'songs.*.iswc' => __('control.product.form.iswc_code'),
            'songs.*.preview_start' => __('control.product.form.preview_start'),
            'songs.*.is_cover' => __('control.product.form.is_cover'),
            'songs.*.remixer_artis' => __('control.product.form.remixer_artis'),
            'songs.*.released_before' => __('control.product.form.released_before'),
            'songs.*.remaster_release_date' => __('control.product.form.remaster_release_date'),
            'songs.*.original_release_date' => __('control.product.form.original_release_date'),
            'main_artists' => __('control.product.form.main_artists'),
            'featuring_artists' => __('control.product.form.featuring_artists'),
            'barcode_type' => __('control.product.form.barcode_type'),
            'upc_code' => __('control.product.form.upc_code'),
            'ean_code' => __('control.product.form.ean_code'),
            'jan_code' => __('control.broadcast.form.jan_code'),
        ];
    }
}
