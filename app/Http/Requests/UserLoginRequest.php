<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_number' => ['required', 'string', 'max:11', 'min:11', 'exists:users,phone_number'],
            'password' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'phone_number.required' => 'The phone field is required.',
            'phone_number.exists' => 'This phone number dose not exists, sign up please',
            'phone_number.max' => 'Try a vaild phone number',
            'phone_number.min' => 'Try a vaild phone number',
            'password.required' => 'The password field is required.',

        ];
    }
}
