<?php

namespace App\Http\Requests\Song;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

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
        dd($this->request->all());
        return [
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string'],
            'version' => ['required', 'string'],
            'main_artists' => ['required', 'exists:artists,id'],
            'featuring_artists' => ['nullable', 'array'],
            'genre_id' => ['required', 'exists:genres,id'],
            'sub_genre_id' => ['required', 'exists:genres,id'],
            'is_instrumental' => ['required', 'boolean'],
            'lyrics_writers' => ['nullable', 'array'],
            'lyrics' => ['nullable', 'array'],
            'preview_start' => ['nullable', 'string'],

            'musicians' => ['nullable', 'array'],
            'musicians.*.id' => ['required', 'exists:users,id'],
            'musicians.*.role' => ['required'],

            'participants' => ['nullable', 'array',],
            'participants.*.id' => ['required', 'exists:users,id'],
            'participants.*.tasks' => ['required'],
            'participants.*.rate' => ['required'],

            'isrc' => ['nullable', 'unique:songs,isrc'],
            'is_explicit' => ['nullable', 'boolean'],
            'iswc' => ['nullable', 'string', 'unique:songs,iswc'],
            'is_cover' => ['nullable', 'boolean'],
            'remixer_artis' => ['nullable', 'exists:artists,id'],
            'released_before' => ['nullable', 'boolean'],


        ];
    }
}

/*
name
genre_id
sub_genre_id
type
size
isrc
is_instrumental
is_explicit
language_id
lyrics
iswc
preview_start
released_before
original_release_date
details
acr_response
created_by
status
status_changed_at
created_at
updated_at
status_changed_by
note
duration
version
 * */
