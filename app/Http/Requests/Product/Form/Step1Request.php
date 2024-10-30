<?php

namespace App\Http\Requests\Product\Form;

use App\Enums\AlbumTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Models\System\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use function Laravel\Prompts\select;

class Step1Request extends FormRequest
{


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
        return match ($this->type) {
            ProductTypeEnum::SOUND => self::soundRules(),
            ProductTypeEnum::VIDEO => self::videoRules(),
            ProductTypeEnum::RINGTONE => self::ringtoneRules(),
        };
    }

    private function soundRules(): array
    {
        return [
            'album_name' => ['required', 'string', 'min:3', 'max:100'],
            'version' => ['required', 'string', 'min:3', 'max:100'],
            'mixed_album' => ['required', 'boolean'],
            'main_artists' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    if ($this->mixed_album && count($value) < 2) {
                        $fail('Derleme albümlerde tek bir albüm için en az 2 sanatçı olmak zorundadır..');
                    }
                    if (!$this->mixed_album && count($value) !== 1) {
                        $fail('Tek sanatçılı albümlerde sadece bir sanatçı olabilir.');
                    }
                }
            ],
            'main_artists.*.id' => ['required', 'integer', 'exists:artists,id'],
            'genre_id' => ['required', 'integer', 'exists:genres,id'],
            'sub_genre_id' => ['required', 'integer', 'exists:genres,id'],
            'format' => ['required', Rule::enum(AlbumTypeEnum::class)],
            'label_id' => ['required', 'integer', 'exists:labels,id'],
            'p_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'c_line' => ['nullable', 'string', 'min:3', 'max:100'],
            'upc_code' => ['nullable', 'string', 'min:3', 'max:100'],
            'ean_code' => ['nullable', 'string', 'min:3', 'max:100'],
            'catalog_number' => ['nullable', 'string', 'min:3', 'max:100'],
            'language_id' => ['required', 'integer', Rule::exists(Country::class, 'id')],
            'main_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    private function videoRules(): array
    {
        return [];
    }

    private function ringtoneRules(): array
    {
        return [];
    }

    public function attributes(): array
    {
        return [
            'type' => __('control.product.fields.type'),
            'album_name' => __('control.product.fields.album_name'),
            'version' => __('control.product.fields.version'),
            'genre_id' => __('control.product.fields.genre_id'),
            'sub_genre' => __('control.product.fields.sub_genre'),
            'format' => __('control.product.fields.format'),
            'label_id' => __('control.product.fields.label_id'),
            'p_line' => __('control.product.fields.p_line'),
            'c_line' => __('control.product.fields.c_line'),
            'upc_code' => __('control.product.fields.upc_code'),
            'ean_code' => __('control.product.fields.ean_code'),
            'catalog_number' => __('control.product.fields.catalog_number'),
            'language_id' => __('control.product.fields.language_id'),
            'main_price' => __('control.product.fields.main_price'),
        ];
    }


}
