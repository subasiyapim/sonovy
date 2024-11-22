<?php

namespace App\Http\Requests\Artist;

use App\Models\System\Country;
use App\Rules\UniquePlatformURL;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ArtistStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('artist_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'about' => ['nullable', 'string', 'max:500'],
            'country_id' => ['nullable', Rule::exists(Country::class, 'id')],
            'ipi_code' => ['nullable'],
            'isni_code' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'website' => ['nullable'],
            'phone' => ['nullable', 'string'],
            'platforms' => ['nullable', 'array', new UniquePlatformURL()],
            'artist_branches' => ['nullable', 'array'],
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('control.artist.fields.name'),
            'country_id' => __('control.artist.fields.country'),
            'ipi_code' => __('control.artist.fields.ipi_code'),
            'isni_code' => __('control.artist.fields.isni_code'),
            'image' => __('control.artist.fields.image'),
            'artist_branches' => __('control.artist.fields.artist_branches'),
            'platforms' => __('control.artist.fields.platforms'),
            'about' => __('control.artist.fields.about'),
            'phone' => __('control.artist.fields.phone'),
        ];
    }
}
