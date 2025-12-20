<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartnerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Add proper authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $partnerId = $this->route('partner');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('partners')->ignore($partnerId), 'alpha_dash'],
            'domain' => ['nullable', 'string', 'max:255', Rule::unique('partners')->ignore($partnerId)],
            'is_active' => ['boolean'],

            // Identity fields
            'primary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,svg', 'max:2048'],
            'font_family' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'primary_color.regex' => 'The primary color must be a valid hex color code (e.g., #ff6b35).',
            'secondary_color.regex' => 'The secondary color must be a valid hex color code (e.g., #4ecdc4).',
            'slug.alpha_dash' => 'The slug may only contain letters, numbers, dashes and underscores.',
            'logo.image' => 'The logo must be an image file.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, jpg, png, gif, svg.',
            'logo.max' => 'The logo may not be greater than 2MB.',
        ];
    }
}
