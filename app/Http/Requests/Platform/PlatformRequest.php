<?php

namespace App\Http\Requests\Platform;

use App\Enums\PlatformTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class PlatformRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('platform_create') || Gate::allows('platform_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method()) {
            'POST' => $this->createRules(),
            'PUT', 'PATCH' => $this->updateRules(),
            default => [],
        };


    }

    public function createRules()
    {
        return [
            'type' => ['required', 'in:'.implode(',', array_keys(PlatformTypeEnum::getTitles()))],
            'name' => ['required', 'string'],
            'visible_name' => ['required', 'string'],
            'status' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
            'url' => ['required', 'string'],
            'authenticators' => ['required', 'array'],
            'authenticators.*.key' => ['required', 'string'],
            'authenticators.*.value' => ['required', 'string'],
            'image' => ['nullable', 'image'],
        ];
    }

    public function updateRules()
    {
        return [
            'type' => ['required', 'in:'.implode(',', array_keys(PlatformTypeEnum::getTitles()))],
            'name' => ['required', 'string'],
            'visible_name' => ['required', 'string'],
            'code' => ['nullable', 'string', 'unique:platforms,code'],
            'status' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
            'url' => ['required', 'string'],
            'authenticators' => ['required', 'array'],
            'authenticators.*.key' => ['required', 'string'],
            'authenticators.*.value' => ['required', 'string'],
            'image' => ['nullable', 'image'],
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => __('control.platform.form.type'),
            'name' => __('control.platform.form.name'),
            'visible_name' => __('control.platform.form.visible_name'),
            'code' => __('control.platform.form.code'),
            'status' => __('control.platform.form.status'),
            'description' => __('control.platform.form.description'),
            'url' => __('control.platform.form.url'),
            'authenticators' => __('control.platform.form.authenticators'),
            'authenticators.*.key' => __('control.platform.form.key'),
            'authenticators.*.value' => __('control.platform.form.value'),
            'image' => __('control.platform.form.image'),
        ];

    }
}
