<?php

namespace App\Http\Requests\Product;

use App\Enums\AlbumTypeEnum;
use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\VideoTypeEnum;
use App\Models\Product;
use App\Models\System\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    private static function stepFour(Product $product): array
    {
        $data = [
            'promotions' => ['array'],
            'promotions.*.language_id' => ['required',],
            'promotions.*.title' => ['required', 'string', 'min:3', 'max:100'],
            'promotions.*.description' => ['required', 'string', 'min:3'],
        ];


        if ($product && !$product->image) {
            $data =
                [
                    //image,mimes:jpeg olacak ve min. 14440 px olabilir max: 3000px olabilir

                    'image' => [
                        'required', 'image', 'mimes:jpeg',
                        'aspect_ratio:1',
                        'min:1440', 'max:3000'
                    ]
                ];
        }

        return array_merge($data, self::common());
    }

    private static function stepThree(Product $product): array
    {
        $data = [
            'production_year' => ['required', 'integer', 'min:1900', 'max:'.date('Y')],
            'previously_released' => ['required', 'boolean'],

            'previous_release_date' => [
                'required_if:previously_released,true',
                'nullable',
                'date', 'before:'.date('Y-m-d')
            ],

            'physical_release_date' => [
                'required', 'date',
                function ($attribute, $value, $fail) use ($product) {
                    // Sadece product modelinde previous_release_date boşsa kontrol yap
                    if (empty($product->physical_release_date)) {
                        if ($value && \Carbon\Carbon::parse($value)->gte(\Carbon\Carbon::today())) {
                            $fail("The {$attribute} must be a date before today.");
                        }
                    }
                }
            ],
            'publishing_country_type' => ['required', Rule::enum(ProductPublishedCountryTypeEnum::class)],
            'published_countries' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($product) {
                    if ($product->publishing_country_type !== ProductPublishedCountryTypeEnum::ALL) {
                        if (count($value) < 1) {
                            $fail("The {$attribute} must have at least 1 item(s) when publishing_country_type is specific.");
                        }
                    }
                }
            ],
            'platforms' => ['array', 'required'],
            'platforms.*.id' => ['required', 'exists:platforms,id'],
            'platforms.*.price' => ['nullable', 'numeric', 'min:0'],
            'platforms.*.pre_order_date' => ['nullable', 'date'],
            'platforms.*.publish_date' => ['required', 'date'],
        ];

        return array_merge($data, self::common());
    }

    private static function stepTwo(): array
    {
        return [];
    }

    private static function stepOne(): array
    {

        $data = [
            'type' => ['required', Rule::enum(ProductTypeEnum::class)],
            'album_name' => ['required', 'string', 'max:250'],
            'version' => ['nullable', 'string', 'min:3', 'max:100'],
            'mixed_album' => ['required', 'boolean'],
            'genre_id' => ['required', 'integer', 'exists:genres,id'],
            'sub_genre_id' => ['required', 'integer', 'exists:genres,id'],
            'format_id' => ['required_if:type,'.ProductTypeEnum::SOUND->value],
            'main_artists' => ['array', 'required_if:mixed_album,false'],
            'featuring_artists' => ['array'],
            'label_id' => ['required', 'exists:labels,id'],
            'p_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'c_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'upc_code' => ['nullable', 'string', 'min:3', 'max:100'],
            'catalog_number' => ['nullable', 'string', 'min:3', 'max:100'],
            'language_id' => ['required', Rule::exists(Country::class, 'id')],
            'main_price' => ['nullable', 'numeric', 'min:0'],
            'video_type' => ['required_if:type,'.ProductTypeEnum::VIDEO->value],
            'description' => ['nullable'],
            'is_for_kids' => ['required_if:type,'.ProductTypeEnum::VIDEO->value],
            'grid_code' => ['required_if:type,'.ProductTypeEnum::RINGTONE->value],
        ];

        return array_merge($data, self::common());
    }

    private static function common(): array
    {
        return [
            'step' => ['required', 'integer', 'min:1', 'max:5'],
        ];

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
        $product = Product::find(request()->route('product')->id);

        return match ($this->step) {
            '1' => self::stepOne(),
            '2' => self::stepTwo(),
            '3' => self::stepThree($product),
            '4' => self::stepFour($product),
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
            'publishing_country_type' => __('control.product.fields.publishing_country_type'),
            'published_countries' => __('control.product.fields.published_countries'),
            'published_countries.*.id' => __('control.product.fields.published_countries'),
            'physical_release_date' => __('control.product.fields.physical_release_date'),
            'previous_release_date' => __('control.product.fields.previous_release_date'),
            'previously_released' => __('control.product.fields.previously_released'),
            'grid_code' => __('control.product.fields.grid_code'),
        ];
    }
}
