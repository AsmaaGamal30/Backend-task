<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'cover_image' => ['required', 'image'],
            'pinned' => ['required', 'boolean'],
            'tags' => ['required', 'array'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'The title is required',
            'title.max' => 'The title is too long',
            'body.required' => 'The body content is required',
            'cover_image.required' => 'The cover image is required',
            'cover_image.image' => 'The cover image must be a valid image file',
            'pinned.required' => 'The pinned status is required',
            'tags.required' => 'At least one tag is required',
            'tags.array' => 'The tags must be an array',
            'tags.*.exists' => 'The selected tag is invalid',
        ];
    }
}