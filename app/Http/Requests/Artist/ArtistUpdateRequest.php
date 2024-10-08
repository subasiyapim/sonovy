<?php

namespace App\Http\Requests\Artist;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

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
            'country_id' => ['required', 'exists:countries,id'],
            'ipi_code' => ['nullable'],
            'isni_code' => ['nullable'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'artist_branches' => ['required', 'array'],
            'website' => ['nullable', 'url'],
            'platforms' => ['nullable', 'array'],
            'description' => ['nullable', 'string', 'max:500'],
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
            'name' => __('panel.artist.form.name'),
            'country_id' => __('panel.artist.form.country'),
            'ipi_code' => __('panel.artist.form.ipi_code'),
            'isni_code' => __('panel.artist.form.isni_code'),
            'image' => __('panel.artist.form.image'),
            'artist_branches' => __('panel.artist.form.artist_branches'),
            'website' => __('panel.artist.form.website'),
            'platforms' => __('panel.artist.form.platforms'),
            'description' => __('panel.artist.form.description'),
        ];
    }
}
