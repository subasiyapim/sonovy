<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductPublishedCountryTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Product;
use App\Models\System\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Validation\Rules\RequiredIf;

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
                        'required',
                        'image',
                        'mimes:jpeg',
                        'aspect_ratio:1',
                        'min:1440',
                        'max:3000'
                    ]
                ];
        }

        return array_merge($data, self::common());
    }


    private static function stepThree(Product $product, $request): array
    {
        $currentYear = date('Y');
        $today = Carbon::today()->format('Y-m-d');

        $data = [
            'production_year' => [
                'nullable',
                'required_if:type,2,false',
                'integer',
                'min:1900',
                'max:' . $currentYear
            ],

            'previously_released' => ['required', 'boolean'],

            'previous_release_date' => [
                'required_if:previously_released,true',
                'nullable',
                'date',
                "before:{$today}"
            ],

            'physical_release_date' => [
                'required',
                'date',
            ],

            'publishing_country_type' => [
                'required',
                Rule::enum(ProductPublishedCountryTypeEnum::class)
            ],

            'published_countries' => [
                'array',
                function ($attribute, $value, $fail) use ($product) {
                    if ($product->type == 2) {
                        return;
                    }
                    if ($product->publishing_country_type !== ProductPublishedCountryTypeEnum::ALL) {
                        if (empty($value) || count($value) < 1) {
                            $fail("The {$attribute} must have at least 1 item(s) when publishing_country_type is specific.");
                        }
                    }
                }
            ],

            'platforms' => ['required', 'array'],
            'platforms.*.id' => ['required', 'exists:platforms,id'],
            'platforms.*.price' => ['nullable', 'numeric', 'min:0'],
            'platforms.*.pre_order_date' => ['nullable', 'date'],
            'platforms.*.publish_date' => ['nullable', 'required_if:type,1,false', 'date'],

            // Yeni koşullu zorunlu alanlar
            'platforms.*.isChecked' => ['nullable', 'boolean'],
            'platforms.*.description' => [
                function ($attribute, $value, $fail) use ($product, $request) {
                    $platforms = $request->input('platforms', []);
                    $index = explode('.', $attribute)[1]; // platforms.0.description -> 0
                    $isChecked = isset($platforms[$index]['isChecked']) ? $platforms[$index]['isChecked'] : false;

                    if ($isChecked == true && $product->type == 2 && empty($value)) {
                        $fail("The {$attribute} alanı muzik video seçili olduğunda zorunludur.");
                    }
                },
            ],
            'platforms.*.content_id' => [
                function ($attribute, $value, $fail) use ($product, $request) {
                    $platforms = $request->input('platforms', []);
                    $index = explode('.', $attribute)[1];
                    $isChecked = isset($platforms[$index]['isChecked']) ? $platforms[$index]['isChecked'] : false;
                    if ($isChecked == true && $product->type == 2 && empty($value)) {
                        $fail("The {$attribute} alanı muzik video seçili olduğunda zorunludur.");
                    }
                },
            ],
            'platforms.*.privacy' => [
                function ($attribute, $value, $fail) use ($product, $request) {
                    $platforms = $request->input('platforms', []);
                    $index = explode('.', $attribute)[1];
                    $isChecked = isset($platforms[$index]['isChecked']) ? $platforms[$index]['isChecked'] : false;

                    if ($isChecked == true && $product->type == 2 && empty($value)) {
                        $fail("The {$attribute} alanı muzik video seçili olduğunda zorunludur.");
                    }
                },
            ],
            'platforms.*.hashtags' => [
                function ($attribute, $value, $fail) use ($product, $request) {
                    $platforms = $request->input('platforms', []);
                    $index = explode('.', $attribute)[1];
                    $isChecked = isset($platforms[$index]['isChecked']) ? $platforms[$index]['isChecked'] : false;

                    if ($isChecked == true && $product->type == 2 && empty($value)) {
                        $fail("The {$attribute} alanı muzik video seçili olduğunda zorunludur.");
                    }
                },
            ],
            'platforms.*.date' => [
                function ($attribute, $value, $fail) use ($product, $request) {
                    $platforms = $request->input('platforms', []);
                    $index = explode('.', $attribute)[1];
                    $isChecked = isset($platforms[$index]['isChecked']) ? $platforms[$index]['isChecked'] : false;

                    if ($isChecked == true && $product->type == 2 && empty($value)) {
                        $fail("The {$attribute} alanı muzik video seçili olduğunda zorunludur.");
                    }
                },
            ],
            'platforms.*.time' => [
                function ($attribute, $value, $fail) use ($product, $request) {
                    $platforms = $request->input('platforms', []);
                    $index = explode('.', $attribute)[1];
                    $isChecked = isset($platforms[$index]['isChecked']) ? $platforms[$index]['isChecked'] : false;

                    if ($isChecked == true && $product->type == 2 && empty($value)) {
                        $fail("The {$attribute} alanı muzik video seçili olduğunda zorunludur.");
                    }
                },
            ],

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
            'format_id' => ['required_if:type,' . ProductTypeEnum::SOUND->value],
            'main_artists' => ['array', 'required_if:mixed_album,false'],
            'featuring_artists' => ['array'],
            'label_id' => ['required', 'exists:labels,id'],
            'p_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'c_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'upc_code' => ['nullable', 'string', 'min:3', 'max:100'],
            'catalog_number' => ['nullable', 'string', 'min:3', 'max:100'],
            'language_id' => ['required', Rule::exists(Country::class, 'id')],
            'main_price' => ['nullable', 'numeric', 'min:0'],

            'description' => ['nullable'],
            'is_for_kids' => ['required_if:type,' . ProductTypeEnum::VIDEO->value],
            'grid_code' => ['required_if:type,' . ProductTypeEnum::RINGTONE->value],
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
        //dd($this->all());
        $product = Product::find(request()->route('product')->id);
        return match ($this->step) {
            '1' => self::stepOne(),
            '2' => self::stepTwo(),
            '3' => self::stepThree($product, $this),
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
            'platforms.*.id' => __('control.product.fields.id'),
            'platforms.*.price' => __('control.product.fields.price'),
            'platforms.*.pre_order_date' => __('control.product.fields.pre_order_date'),
            'platforms.*.publish_date' => __('control.product.fields.publish_date'),
            'platforms.*.isChecked' => __('control.product.fields.isChecked'),
            'platforms.*.description' => __('control.product.fields.description'),
            'platforms.*.content_id' => __('control.product.fields.content_id'),
            'platforms.*.privacy' => __('control.product.fields.privacy'),
            'platforms.*.hashtags' => __('control.product.fields.hashtags'),
            'platforms.*.date' => __('control.product.fields.date'),
            'platforms.*.time' => __('control.product.fields.time'),
        ];
    }
}
