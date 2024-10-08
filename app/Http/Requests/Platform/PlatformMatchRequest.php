<?php

namespace App\Http\Requests\Platform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class PlatformMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('platform_match');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'master_id' => ['required', 'exists:platforms,id',],
            'slave_id' => ['required', 'exists:platforms,id',],
        ];
    }


    public function attributes(): array
    {
        return [
            'master_id' => 'Kalacak Platform',
            'slave_id' => 'Silinecek Platform',
        ];
    }
}
