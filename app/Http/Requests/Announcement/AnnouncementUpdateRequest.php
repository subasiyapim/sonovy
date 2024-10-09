<?php

namespace App\Http\Requests\Announcement;

use App\Enums\AnnouncementReceiversEnum;
use App\Enums\AnnouncementTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class AnnouncementUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('announcement_edit');
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            //'type' => ['required', new Enum(AnnouncementTypeEnum::class)],
            'type' => ['required', 'array', 'min:1', 'max:4'],
            'type.*' => [new Enum(AnnouncementTypeEnum::class), 'distinct'],
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
            'name' => __('control.artist.form.name'),
            'country_id' => __('control.artist.form.country'),
            'ipi_code' => __('control.artist.form.ipi_code'),
            'isni_code' => __('control.artist.form.isni_code'),
            'image' => __('control.artist.form.image'),
            'artist_branches' => __('control.artist.form.artist_branches'),
            'website' => __('control.artist.form.website'),
        ];
    }
}
