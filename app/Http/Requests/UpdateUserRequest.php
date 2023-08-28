<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'unique:users'],
            'role' => ['in:admin,user'],
            'old_password' => ['string', 'max:255'],
            'password' => ['string', 'confirmed', Password::min(8)->letters()->numbers()->mixedCase()],
            'facebook' => ['url'],
            'instagram' => ['url'],
            'twitter' => ['url'],
            'linkedin' => ['url'],
        ];
    }
}
