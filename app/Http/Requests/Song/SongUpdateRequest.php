<?php

namespace App\Http\Requests\Song;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SongUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('song_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string'],
            'version' => ['nullable', 'string'],
            'main_artists' => ['required', 'exists:artists,id'],
            'featuring_artists' => ['required', 'array'],
            'genre_id' => ['required', 'exists:genres,id'],
            'sub_genre_id' => ['required', 'exists:genres,id'],
            'is_instrumental' => ['required', 'boolean'],
            'lyrics_writers' => [
                'array',
                'min:1',
            ],
            'lyrics_writers.*' => ['required_if:is_instrumental,false', 'required_if:is_instrumental,0'],
            'lyrics' => ['nullable'],
            'preview_start' => ['nullable'],

            'musicians' => ['nullable', 'array'],
            'musicians.*.id' => ['required', 'exists:users,id'],
            'musicians.*.role' => ['required'],

            'participants' => ['nullable', 'array',],
            'participants.*.id' => ['required', 'exists:users,id'],
            'participants.*.tasks' => ['required'],
            'participants.*.rate' => ['required'],

            'isrc' => ['nullable', 'unique:songs,isrc,'.$this->song->id],
            'is_explicit' => ['nullable', 'boolean'],
            'iswc' => ['nullable', 'string', 'unique:songs,iswc'],
            'is_cover' => ['nullable', 'boolean'],
            'remixer_artis' => ['nullable', 'exists:artists,id'],
            'released_before' => ['nullable', 'boolean'],


        ];
    }
}
