<?php

namespace App\Http\Requests\Api\v1\Review;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'rating'  => 'required|integer|between:1,5',
            'comment' => 'required|string',
        ];
    }
}
