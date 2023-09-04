<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSneakerRequest extends FormRequest
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
            'title' => [ 'string', 'max:255', 'unique:sneakers,title, ' . $this->id],
            'brand' => ['string'],
            'colorway' => ['string'],
            'gender' => ['string'],
            'retailPrice' => ['numeric'],
            'quantity' => ['numeric'],
            'releaseDate' => ['date'],
            'imageUrl' => ['image', 'mimes:png,jpg,jpeg,svg', 'max:5120'],
            'smallImageUrl' => ['image', 'mimes:png,jpg,jpeg,svg', 'max:5120'],
            'thumbUrl' => ['image', 'mimes:png,jpg,jpeg,svg', 'max:5120'],
        ];
    }
}
