<?php

namespace App\Http\Requests\Announcement\Template;

use App\Enums\AnnouncementReceiversEnum;
use App\Enums\AnnouncementTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class AnnouncementTemplateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('announcement_template_create');
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
            'type' => ['required', new Enum(AnnouncementTypeEnum::class)],
            'content' => ['required', 'string'],
            'send_type' => ['required', 'in:automatic,manual'],
        ];
    }
}
