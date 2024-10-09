<?php

namespace App\Http\Requests\Artist;

use App\Models\System\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ArtistUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('artist_edit');
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
            'country_id' => ['required', Rule::exists(Country::class, 'id')],
            'ipi_code' => ['nullable'],
            'isni_code' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'website' => ['nullable', 'url'],
            'phone' => ['nullable', 'string'],
            'platforms' => ['nullable', 'array'],
            'artist_branches' => ['required', 'array'],
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
            'name' => __('control.artist.form.name'),
            'country_id' => __('control.artist.form.country'),
            'ipi_code' => __('control.artist.form.ipi_code'),
            'isni_code' => __('control.artist.form.isni_code'),
            'image' => __('control.artist.form.image'),
            'artist_branches' => __('control.artist.form.artist_branches'),
            'website' => __('control.artist.form.website'),
            'platforms' => __('control.artist.form.platforms'),
            'about' => __('control.artist.form.about'),
            'phone' => __('control.artist.form.phone'),
        ];
    }
}
