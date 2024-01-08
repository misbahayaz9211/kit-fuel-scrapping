<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country' => ['nullable', 'string'],
        ];
    }
}
