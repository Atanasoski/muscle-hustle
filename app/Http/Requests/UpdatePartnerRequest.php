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
            'primary_color' => ['required', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'secondary_color' => ['required', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,svg', 'max:2048'],
            'font_family' => ['nullable', 'string', 'max:255'],
            'background_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'card_background_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'text_primary_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'text_secondary_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'text_on_primary_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'success_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'warning_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'danger_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'accent_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'border_color' => ['nullable', 'string', 'regex:/^\d{1,3},\d{1,3},\d{1,3}$/'],
            'background_pattern' => ['nullable', 'string', 'max:255'],
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
            'primary_color.regex' => 'The primary color must be in RGB format (e.g., 255,107,53).',
            'secondary_color.regex' => 'The secondary color must be in RGB format (e.g., 78,205,196).',
            'background_color.regex' => 'The background color must be in RGB format (e.g., 255,255,255).',
            'card_background_color.regex' => 'The card background color must be in RGB format (e.g., 248,249,250).',
            'text_primary_color.regex' => 'The text primary color must be in RGB format (e.g., 33,37,41).',
            'text_secondary_color.regex' => 'The text secondary color must be in RGB format (e.g., 108,117,125).',
            'text_on_primary_color.regex' => 'The text on primary color must be in RGB format (e.g., 255,255,255).',
            'success_color.regex' => 'The success color must be in RGB format (e.g., 16,220,96).',
            'warning_color.regex' => 'The warning color must be in RGB format (e.g., 255,206,0).',
            'danger_color.regex' => 'The danger color must be in RGB format (e.g., 240,65,65).',
            'accent_color.regex' => 'The accent color must be in RGB format (e.g., 138,195,74).',
            'border_color.regex' => 'The border color must be in RGB format (e.g., 222,226,230).',
            'slug.alpha_dash' => 'The slug may only contain letters, numbers, dashes and underscores.',
            'logo.image' => 'The logo must be an image file.',
            'logo.mimes' => 'The logo must be a file of type: jpeg, jpg, png, gif, svg.',
            'logo.max' => 'The logo may not be greater than 2MB.',
        ];
    }
}
