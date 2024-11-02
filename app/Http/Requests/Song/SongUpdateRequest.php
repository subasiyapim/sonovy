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
        return [
            'name' => ['required', 'string'],
            'genre_id' => ['required', 'exists:genres,id'],
            'sub_genre_id' => ['required', 'exists:genres,id'],
            'type' => ['nullable', 'in:1,2'],
            'isrc' => ['nullable', 'unique:songs,isrc'],
            'is_instrumental' => ['required', 'boolean'],
            'is_explicit' => ['nullable', 'boolean'],
            'language_id' => ['nullable', 'exists:countries,id'],
            'lyrics' => ['nullable', 'string'],
            'lyrics_writers' => ['nullable', 'array'],
            'lyrics_writers.*.id' => ['exists:artists,id'],
            'lyrics_writers.*.tasks' => ['required'],
            'lyrics_writers.*.rate' => ['required', 'numeric'],
            'iswc' => ['nullable', 'string', 'unique:songs,iswc'],
            'preview_start' => ['nullable', 'string'],
            'is_cover' => ['nullable', 'boolean'],
            'remixer_artis' => ['nullable', 'exists:artists,id'],
            'released_before' => ['nullable', 'boolean'],
            'songs.*.original_release_date' => ['nullable', 'date'],
        ];
    }
}
