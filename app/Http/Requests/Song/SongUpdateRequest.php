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
        //dd($this->all());
        return [
            'product_id' => ['required', 'exists:products,id'],
            'name' => ['required', 'string'],
            'version' => ['nullable', 'string'],
            'main_artists' => ['required', 'exists:artists,id'],
            'featuring_artists' => ['nullable', 'array'],
            'genre_id' => ['required', 'exists:genres,id'],
            'sub_genre_id' => ['required', 'exists:genres,id'],
            'is_instrumental' => ['required', 'boolean'],

            'lyrics_writers' => ['required_if:is_instrumental,false'],

            'lyrics' => ['nullable'],
            'preview_start' => ['nullable'],

            'musicians' => ['nullable', 'array'],
            'musicians.*.name' => ['required'],
            'musicians.*.role_id' => ['required', 'exists:artist_branches,id'],

            'composers' => ['nullable', 'array'],

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

    public function attributes(): array
    {
        return [
            'product_id' => __('control.song.fields.product_id'),
            'name' => __('control.song.fields.name'),
            'version' => __('control.song.fields.version'),
            'main_artists' => __('control.song.fields.main_artists'),
            'featuring_artists' => __('control.song.fields.featuring_artists'),
            'genre_id' => __('control.song.fields.genre_id'),
            'sub_genre_id' => __('control.song.fields.sub_genre_id'),
            'is_instrumental' => __('control.song.fields.is_instrumental'),
            'lyrics_writers' => __('control.song.fields.lyrics_writers'),
            'lyrics' => __('control.song.fields.lyrics'),
            'preview_start' => __('control.song.fields.preview_start'),
            'musicians' => __('control.song.fields.musicians'),
            'composers' => __('control.song.fields.composers'),
            'participants' => __('control.song.fields.participants'),
            'isrc' => __('control.song.fields.isrc'),
            'is_explicit' => __('control.song.fields.is_explicit'),
            'iswc' => __('control.song.fields.iswc'),
            'is_cover' => __('control.song.fields.is_cover'),
            'remixer_artis' => __('control.song.fields.remixer_artis'),
            'released_before' => __('control.song.fields.released_before'),
        ];
    }

    public function messages(): array
    {
        return [
            'lyrics_writers.required_if' => 'Söz yazarı eğer enstrümantal değilse zorunludur.',
        ];
    }
}
