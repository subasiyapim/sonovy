<?php

namespace App\Http\Requests\Announcement;

use App\Enums\AnnouncementReceiversEnum;
use App\Enums\AnnouncementTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class AnnouncementStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('announcement_create');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'type' => array_keys(array_filter($this->input('type'), 'strlen')),
        ]);
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
            //'type' => ['required', new Enum(AnnouncementTypeEnum::class)],
            'type' => ['required', 'array', 'min:1', 'max:4'],
            'type.*' => ['nullable', new Enum(AnnouncementTypeEnum::class), 'distinct'],
            'template_id' => ['nullable', 'exists:announcement_templates,id'],
            'content' => ['required', 'string'],
            'send_type' => ['required', 'in:0,1'],
            'from' => ['nullable', 'required_if:send_type,0', 'date', 'after_or_equal:today'],
            'to' => ['nullable', 'required_if:send_type,0', 'date', 'after:today'],
            'receivers' => ['required', new Enum(AnnouncementReceiversEnum::class)],
            'selected.*' => ['nullable', 'required_if:receivers,selected', 'exists:users,id'],
            'exceptions.*' => ['nullable', 'required_if:receivers,all_but', 'exists:users,id'],
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
            'name' => __('control.announcement.form.name'),
            'type' => __('control.announcement.form.type'),
            'content' => __('control.announcement.form.content'),
            'send_type' => __('control.announcement.form.send_type'),
            'from' => __('control.announcement.form.from'),
            'to' => __('control.announcement.form.to'),
            'receivers' => __('control.announcement.form.receivers'),
        ];
    }
}
