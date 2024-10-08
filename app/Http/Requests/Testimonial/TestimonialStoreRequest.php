<?php

namespace App\Http\Requests\Testimonial;

use App\Services\LocaleService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TestimonialStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('testimonial_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $langs = LocaleService::getLocalizationList();
        $arr = [
            'name' => ['required', 'string'],
            'role' => ['required', 'string'],
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg'],
            'text' => 'required|array'
        ];

        foreach ($langs as $lang) {
            $arr['text.' . $lang] = ['required', 'string', 'max:1000'];
        }
        //dd($arr, $this->all());
        return $arr;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('panel.testimonials.form.name'),
            'role' => __('panel.testimonials.form.role'),
            'avatar' => __('panel.testimonials.form.avatar'),
            'text' => __('panel.testimonials.form.text'),
        ];
    }
}
