<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendSmsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    public function rules(): array
    {
        return [
            'phones'   => ['required', 'array', 'min:1'],
            'phones.*' => ['required', 'string', 'regex:/^\+998[0-9]{9}$/'],
            'message'  => ['required', 'string', 'min:1', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'phones.required'   => 'The phones field is required.',
            'phones.array'      => 'phones must be an array.',
            'phones.*.regex'    => 'Each phone must be in Uzbekistan format: +998XXXXXXXXX',
            'message.required'  => 'The message field is required.',
            'message.max'       => 'The message must not exceed 500 characters.',
        ];
    }
}
