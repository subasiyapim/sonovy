<?php

namespace App\Http\Requests\Song;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SongChangeStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('song_change_status');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|exists:songs,id',
            'note' => 'required|string',
            'status' => 'required|in:1,2,3,4',
        ];
    }

    public function attributes()
    {
        return [
            'id' => __('control.song.form.id'),
            'note' => __('control.song.form.note'),
            'status' => __('control.song.form.status'),
        ];
    }
}
