<?php

namespace App\Http\Requests\ArtistBranches;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ArtistBranchesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('artist_branch_create');
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

    private function createRules()
    {
        return [
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|unique:artist_branch_translations,name',
            'translations.*.locale' => 'nullable|string|in:'.implode(',', LocaleService::getLocalizationList()),
        ];

    }

    private function updateRules()
    {
        return [
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|unique:artist_branch_translations,name,'.$this->route('artist_branch')->id.',artist_branch_id',
            'translations.*.locale' => 'nullable|string|in:'.implode(',', LocaleService::getLocalizationList()),
        ];
    }

    public function attributes()
    {
        return [
            'translations.*.name' => __('control.artist-branch.form.name'),
            'translations.*.locale' => __('control.artist-branch.fields.locale'),
        ];

    }
}
