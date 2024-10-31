<?php

namespace App\Http\Requests\Product;

use App\Enums\AlbumTypeEnum;
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

    private static function stepThree(): array
    {
        return [];
    }

    private static function stepTwo(): array
    {
        return [];
    }

    private static function stepOne(): array
    {
        return [
            'type' => ['required', Rule::enum(ProductTypeEnum::class)],
            'album_name' => ['required', 'string', 'min:3', 'max:100'],
            'version' => ['required', 'string', 'min:3', 'max:100'],
            'mixed_album' => ['required', 'boolean'],
            'genre_id' => ['required', 'integer', 'exists:genres,id'],
            'sub_genre_id' => ['required', 'integer', 'exists:genres,id'],
            'format_id' => ['required', Rule::enum(AlbumTypeEnum::class)],
            'main_artists' => [
                'required', 'array', function ($attribute, $value, $fail) {
                    $minCount = $this->mixed_album ? 2 : 1;

                    if (count($value) < $minCount) {
                        $fail("The {$attribute} must have at least {$minCount} item(s) when mixed_album is ".($this->mixed_album ? 'true' : 'false').".");
                    }

                }
            ],
            'main_artists.*.id' => ['required', 'integer', 'exists:artists,id'],
            'featuring_artists' => ['array'],
            'featuring_artists.*.id' => ['required', 'integer', 'exists:artists,id'],
            'label_id' => ['required', 'exists:labels,id'],
            'p_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'c_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'upc_code' => ['nullable', 'string', 'min:3', 'max:100'],
            'catalog_number' => ['nullable', 'string', 'min:3', 'max:100'],
            'language_id' => ['required', Rule::exists(Country::class, 'id')],
            'main_price' => ['required', 'numeric', 'min:0'],
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
        return match ($this->step) {
            1 => self::stepOne(),
            2 => self::stepTwo(),
            3 => self::stepThree(),
            4 => self::stepFour(),
            5 => self::stepFive(),
        };
    }


}