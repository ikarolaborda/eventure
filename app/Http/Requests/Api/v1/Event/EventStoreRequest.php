<?php

namespace App\Http\Requests\Api\v1\Event;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;


#[OA\Schema(
    schema: "EventStoreRequest",
    description: "Request body for creating an event",
    required: ["title", "description", "start_date", "end_date", "booking_deadline", "attendee_limit", "price", "location"]
)]
class EventStoreRequest extends FormRequest
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
    #[OA\Property(property: "start_date", description: "Start date/time of the event, must be in the future", type: "string", format: "date-time")]
    #[OA\Property(property: "end_date", description: "End date/time of the event, must be after start_date", type: "string", format: "date-time")]
    #[OA\Property(property: "booking_deadline", description: "Deadline for booking, must be before start_date", type: "string", format: "date-time")]
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'booking_deadline' => 'required|date|before:start_date',
            'attendee_limit' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255'
        ];
    }
}
