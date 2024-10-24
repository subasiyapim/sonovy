<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('broadcast_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
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
            'status' => ['required'],
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
            'image' => ['nullable', 'mimes:jpeg,jpg,png', 'max:10000'],
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
            'songs.*.isrc' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    if (!array_key_exists($index, $this->songs) || !array_key_exists('id', $this->songs[$index])) {
                        $fail('The song id is missing.');
                    } else {
                        $songId = $this->songs[$index]['id'];
                        if (DB::table('songs')->where('isrc', $value)->where('id', '!=',
                            $songId)->exists()) {
                            $fail('The ISRC has already been taken.');
                        }
                    }
                }
            ],
            'songs.*.is_instrumental' => ['required', 'boolean'],
            'songs.*.is_explicit' => ['required', 'boolean'],
            'songs.*.language_id' => ['required', 'exists:countries,id'],
            'songs.*.lyrics' => ['nullable', 'string'],
            'songs.*.lyrics_writers' => ['nullable', 'array'],
            'songs.*.lyrics_writers.*.id' => ['exists:users,id'],
            'songs.*.lyrics_writers.*.tasks' => ['required'],
            'songs.*.lyrics_writers.*.rate' => ['required', 'numeric'],
            'songs.*.iswc' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    if (!array_key_exists($index, $this->songs) || !array_key_exists('id', $this->songs[$index])) {
                        $fail('The song id is missing.');
                    } else {
                        $songId = $this->songs[$index]['id'];
                        if (DB::table('songs')->where('isrc', $value)->where('id', '!=',
                            $songId)->exists()) {
                            $fail('The ISRC has already been taken.');
                        }
                    }
                }
            ],
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
            'type' => __('control.broadcast.form.type'),
            'label_id' => __('control.broadcast.label_company'),
            'right_to_perform_work' => __('control.broadcast.form.right_to_perform_work'),
            'has_audiovisual_rights' => __('control.broadcast.form.has_audiovisual_rights'),
            'copyright_for_publication_image' => __('control.broadcast.form.copyright_for_publication_image'),
            'publisher_name' => __('control.broadcast.form.publisher_name'),
            'is_for_children' => __('control.broadcast.form.is_for_children'),
            'youtube_channel' => __('control.broadcast.form.youtube_channel'),
            'youtube_channel_theme' => __('control.broadcast.form.youtube_channel_theme'),
            'description' => __('control.broadcast.form.description'),
            'hashtags' => __('control.broadcast.form.hashtags'),
            'hashtags.*' => __('control.broadcast.form.hashtags.*'),
            'name' => __('control.broadcast.form.name'),
            'p_line' => __('control.broadcast.form.p_line'),
            'image' => __('control.broadcast.form.image'),
            'genre_id' => __('control.broadcast.form.genre'),
            'sub_genre_id' => __('control.broadcast.form.sub_genre'),
            'is_compilation_publication' => __('control.broadcast.form.is_compilation_publication'),
            'catalog_number' => __('control.broadcast.form.catalog_number'),
            'language_id' => __('control.broadcast.form.language'),
            'release_date' => __('control.broadcast.form.release_date'),
            'original_release_date' => __('control.broadcast.form.original_release_date'),
            'remaster_release_date' => __('control.broadcast.form.remaster_release_date'),
            'has_been_released' => __('control.broadcast.form.has_been_released'),
            'publish_country_type' => __('control.broadcast.form.publish_country_type'),
            'is_publication_date_the_same' => __('control.broadcast.form.is_publication_date_the_same'),
            'publication_date' => __('control.broadcast.form.publication_date'),
            'add_new_to_streaming_platforms' => __('control.broadcast.form.add_new_to_streaming_platforms'),
            'songs' => __('control.broadcast.form.songs'),
            'songs.*.name' => __('control.broadcast.form.name'),
            'songs.*.genre_id' => __('control.broadcast.form.genre'),
            'songs.*.sub_genre_id' => __('control.broadcast.form.sub_genre'),
            'songs.*.isrc' => __('control.broadcast.form.isrc'),
            'songs.*.is_instrumental' => __('control.broadcast.form.is_instrumental'),
            'songs.*.is_explicit' => __('control.broadcast.form.is_explicit'),
            'songs.*.language_id' => __('control.broadcast.form.language'),
            'songs.*.lyrics' => __('control.broadcast.form.lyrics'),
            'songs.*.lyrics_writers' => __('control.broadcast.form.lyrics_writers'),
            'songs.*.lyrics_writers.*.id' => __('control.broadcast.form.lyrics_writers.*.id'),
            'songs.*.lyrics_writers.*.tasks' => __('control.broadcast.form.lyrics_writers.*.tasks'),
            'songs.*.lyrics_writers.*.rate' => __('control.broadcast.form.lyrics_writers.*.rate'),
            'songs.*.iswc' => __('control.broadcast.form.iswc_code'),
            'songs.*.preview_start' => __('control.broadcast.form.preview_start'),
            'songs.*.is_cover' => __('control.broadcast.form.is_cover'),
            'songs.*.remixer_artis' => __('control.broadcast.form.remixer_artis'),
            'songs.*.released_before' => __('control.broadcast.form.released_before'),
            'songs.*.remaster_release_date' => __('control.broadcast.form.remaster_release_date'),
            'songs.*.original_release_date' => __('control.broadcast.form.original_release_date'),
            'main_artists' => __('control.broadcast.form.main_artists'),
            'featuring_artists' => __('control.broadcast.form.featuring_artists'),
            'barcode_type' => __('control.broadcast.form.barcode_type'),
            'upc_code' => __('control.broadcast.form.upc_code'),
            'ean_code' => __('control.broadcast.form.ean_code'),
            'jan_code' => __('control.broadcast.form.jan_code'),
        ];
    }
}
