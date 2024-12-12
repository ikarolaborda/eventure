<?php

namespace App\Http\Requests\Api\v1\Review;

use Illuminate\Foundation\Http\FormRequest;

class ReviewUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'rating'  => 'sometimes|required|integer|between:1,5',
            'comment' => 'sometimes|required|string',
        ];
    }
}
