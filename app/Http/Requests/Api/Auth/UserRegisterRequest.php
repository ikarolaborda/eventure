<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UserRegisterRequest",
    required: ["name", "email", "password"],
    properties: [
        new OA\Property(property: "name", type: "string"),
        new OA\Property(property: "email", type: "string", format: "email"),
        new OA\Property(property: "password", type: "string", format: "password")
    ],
    type: "object"
)]
class UserRegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:120|unique:users',
            'password' => 'required|string|min:8|confirmed',

        ];
    }


}
