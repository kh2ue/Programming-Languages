<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'phone_number' => 'required|string|min:10|max:10',
            'password' => 'required|string|min:8|max:16',
          ///  'role' => 'string|in:Admin,Customer',
            'first_name' => 'string|min:2|max:20',
            'last_name' => 'string|min:2|max:20',
            'location' => 'string|min:2|max:20',
        ];
    }
}
