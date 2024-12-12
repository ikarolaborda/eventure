<?php

namespace App\Http\Requests\Api\v1\Event;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;
#[OA\Schema(
    schema: "EventUpdateRequest",
    description: "Request body for updating an event. All fields are optional and only validated if present."
)]
class EventUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    #[OA\Property(property: "title", description: "Event title", type: "string", maxLength: 255)]
    #[OA\Property(property: "description", description: "Event description", type: "string")]
    #[OA\Property(property: "start_date", description: "Date/time of the event, must be in the future", type: "string", format: "date-time")]
    #[OA\Property(property: "end_date", description: "End date/time of the event, must be after date_time", type: "string", format: "date-time")]
    #[OA\Property(property: "booking_deadline", description: "Deadline for booking, must be before date_time", type: "string", format: "date-time")]
    #[OA\Property(property: "attendee_limit", description: "Maximum number of attendees", type: "integer", minimum: 1)]
    #[OA\Property(property: "price", description: "Price of the event", type: "number", format: "float", minimum: 0)]
    #[OA\Property(property: "location", description: "Location of the event", type: "string", maxLength: 255)]

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */

    public function rules(): array
    {
        return [
            'title'            => 'sometimes|required|string|max:255',
            'description'      => 'sometimes|required|string',
            'start_date'        => 'sometimes|required|date|after:now',
            'end_date'          => 'sometimes|required|date|after:now',
            'booking_deadline' => 'sometimes|required|date|before:date_time',
            'attendee_limit'   => 'sometimes|required|integer|min:1',
            'price'            => 'sometimes|required|numeric|min:0',
            'location'         => 'sometimes|required|string|max:255',
        ];
    }
}
