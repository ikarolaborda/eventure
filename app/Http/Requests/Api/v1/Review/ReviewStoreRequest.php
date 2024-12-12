<?php

namespace App\Http\Requests\Api\v1\Review;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReviewStoreRequest",
    description: "Request body for creating a new review",
    required: ["rating", "comment"]
)]
class ReviewStoreRequest extends FormRequest
{
    #[OA\Property(property: "rating", description: "Rating from 1 to 5", type: "integer", format: "int32")]
    #[OA\Property(property: "comment", description: "Review comment", type: "string")]
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
