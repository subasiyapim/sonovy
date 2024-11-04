<?php

namespace App\Http\Requests\Product;

use App\Enums\AlbumTypeEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Models\System\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    private static function stepFive(): array
    {
        return [];
    }

    private static function stepFour(): array
    {
        return [];
    }

    private static function stepThree($published_country_type): array
    {
        $data = [
            'production_year' => ['required', 'integer', 'min:1900', 'max:'.date('Y')],
            'published_country_type' => ['required', Rule::enum(ProductPublishedCountryTypeEnum::class)],
            'published_countries' => [
                'required', 'array', function ($attribute, $value, $fail) use ($published_country_type) {
                    if ($published_country_type !== ProductPublishedCountryTypeEnum::ALL) {
                        if (count($value) < 1) {
                            $fail("The {$attribute} must have at least 1 item(s) when published_country_type is specific.");
                        }
                    }
                }
            ],
            'published_countries.*.id' => ['required', Rule::exists(Country::class, 'id')],
        ];

        return array_merge($data, self::common());
    }

    private static function stepTwo(): array
    {
        return [];
    }

    private static function stepOne($mixed_album = false): array
    {
        // dd(request()->all());
        $data = [
            'type' => ['required', Rule::enum(ProductTypeEnum::class)],
            'album_name' => ['required', 'string', 'min:3', 'max:100'],
            'version' => ['required', 'string', 'min:3', 'max:100'],
            'mixed_album' => ['required', 'boolean'],
            'genre_id' => ['required', 'integer', 'exists:genres,id'],
            'sub_genre_id' => ['required', 'integer', 'exists:genres,id'],
            'format_id' => ['required', Rule::enum(AlbumTypeEnum::class)],
            'main_artists' => ['array', 'required_if:mixed_album,false'],
            'featuring_artists' => ['array'],
            'label_id' => ['required', 'exists:labels,id'],
            'p_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'c_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'upc_code' => ['nullable', 'string', 'min:3', 'max:100'],
            'catalog_number' => ['nullable', 'string', 'min:3', 'max:100'],
            'language_id' => ['required', Rule::exists(Country::class, 'id')],
            'main_price' => ['nullable', 'numeric', 'min:0'],
        ];

        return array_merge($data, self::common());
    }

    private static function common(): array
    {
        return ['step' => ['required', 'integer', 'min:1', 'max:5']];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('product_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->step) {
            '1' => self::stepOne($this->mixed_album),
            '2' => self::stepTwo(),
            '3' => self::stepThree($this->published_country_type),
            '4' => self::stepFour(),
            '5' => self::stepFive(),
        };
    }

    public function attributes(): array
    {
        return [
            'type' => __('control.product.fields.type'),
            'album_name' => __('control.product.fields.album_name'),
            'version' => __('control.product.fields.version'),
            'mixed_album' => __('control.product.fields.mixed_album'),
            'genre_id' => __('control.product.fields.genre_id'),
            'sub_genre_id' => __('control.product.fields.sub_genre_id'),
            'format_id' => __('control.product.fields.format_id'),
            'main_artists' => __('control.product.fields.main_artists'),
            'main_artists.*.id' => __('control.product.fields.main_artists'),
            'featuring_artists' => __('control.product.fields.featuring_artists'),
            'featuring_artists.*.id' => __('control.product.fields.featuring_artists'),
            'label_id' => __('control.product.fields.label_id'),
            'p_line' => __('control.product.fields.p_line'),
            'c_line' => __('control.product.fields.c_line'),
            'upc_code' => __('control.product.fields.upc_code'),
            'catalog_number' => __('control.product.fields.catalog_number'),
            'language_id' => __('control.product.fields.language_id'),
            'main_price' => __('control.product.fields.main_price'),
            'production_year' => __('control.product.fields.production_year'),
            'published_country_type' => __('control.product.fields.published_country_type'),
            'published_countries' => __('control.product.fields.published_countries'),
            'published_countries.*.id' => __('control.product.fields.published_countries'),
        ];
    }

}
