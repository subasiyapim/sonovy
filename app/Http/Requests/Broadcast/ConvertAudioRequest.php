<?php

namespace App\Http\Requests\Broadcast;

use App\Enums\YoutubeChannelThemeEnum;
use Illuminate\Foundation\Http\FormRequest;

class ConvertAudioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'release_date' => 'required|date',
            'timezone_id' => 'required|exists:timezones,id',
            'release_time' => 'required|date_format:H:i',
            'channel_theme_id' => 'required',
            'broadcast_id' => 'required|exists:broadcasts,id',
            'song_id' => 'required|exists:songs,id',
        ];
    }

    public function attributes()
    {
        return [
            'release_date' => 'Release Date',
            'timezone_id' => 'Time Zone',
            'release_time' => 'Release Time',
            'channel_theme_id' => 'Channel Theme',
            'broadcast_id' => 'Broadcast',
            'song_id' => 'Song',
        ];
    }
}
