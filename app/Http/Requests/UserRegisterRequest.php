<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:11', 'min:11', 'unique:users,phone_number'],
            'password' => ['required', 'confirmed', 'min:5', 'max:24']
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'This name is too long, try shorter one',
            'phone_number.required' => 'The phone field is required.',
            'phone_number.max' => 'Try a vaild phone number',
            'phone_number.min' => 'Try a vaild phone number',
            'phone_number.unique' => 'This phone number already exist, Try another one or login',
            'password.required' => 'The password field is required.',
            'password.confirmed' => 'Password confirmation dose not match.',
            'password.min' => 'Password is too short',
            'password.max' => 'Password is too long',
        ];
    }
}