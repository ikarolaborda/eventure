<?php

namespace App\Http\Requests\Api\v1\Review;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReviewUpdateRequest",
    description: "Request body for updating a review. All fields optional, only validated if present."
)]
class ReviewUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return auth()->check();
    }

    #[OA\Property(property: "rating", description: "Rating from 1 to 5", type: "integer", format: "int32", nullable: true)]
    #[OA\Property(property: "comment", description: "Review comment", type: "string", nullable: true)]

    public function rules(): array
    {
        return [
            'rating'  => 'sometimes|required|integer|between:1,5',
            'comment' => 'sometimes|required|string',
        ];
    }
}
