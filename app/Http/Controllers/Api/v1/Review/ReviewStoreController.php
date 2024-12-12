<?php

namespace App\Http\Controllers\Api\v1\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Review\ReviewStoreRequest;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReservationRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ReviewStoreController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

    #[OA\Post(
        path: "/api/v1/events/{eventId}/reviews",
        operationId: "createReview",
        summary: "Create a new review for an event the user attended",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ReviewStoreRequest")
        ),
        tags: ["Reviews"],
        parameters: [
            new OA\Parameter(
                name: "eventId",
                description: "ID of the event to review",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Review created",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                    new OA\Property(property: "review", type: "object")
                ])
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized"),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Event not found"),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error or event not ended/user not attended")
        ]
    )]
    public function __invoke(ReviewStoreRequest $request, int $eventId): Response
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $event = $this->eventRepository->find($eventId);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if event has ended
        if (now()->lessThanOrEqualTo($event->end_date)) {
            return response()->json(['error' => 'You cannot review this event before it ends'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check if user attended
        if (!$this->reservationRepository->userHasReservation($user, $event)) {
            return response()->json(['error' => 'You did not attend this event'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $review = $this->reviewRepository->createForEvent($user, $event, $request->validated());

        return response()->json(['message' => 'Review created successfully', 'review' => $review], Response::HTTP_CREATED);
    }
}
