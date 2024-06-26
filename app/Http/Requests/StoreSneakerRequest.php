<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSneakerRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', 'unique:sneakers'],
            'brand' => ['required', 'string'],
            'colorway' => ['required', 'string'],
            'gender'=> ['required', 'string'],
            'retailPrice' => ['required', 'numeric'],
            'releaseDate' => ['required', 'date'],
            'quantity' => ['required','numeric','gte:0'],
            'imageUrl' => ['required', 'image','mimes:png,jpg,jpeg,svg','max:5120'],
            'smallImageUrl'=> ['required', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5120'],
            'thumbUrl'=> ['required', 'image', 'mimes:png,jpg,jpeg,svg', 'max:5120'],
        ];
    }
}
