<?php

namespace App\Http\Controllers\Api\v1\Review;

use App\Http\Controllers\Controller;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ReviewDestroyController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

    #[OA\Delete(
        path: "/api/v1/events/{eventId}/reviews/{reviewId}",
        operationId: "deleteReview",
        summary: "Delete a review",
        security: [["bearerAuth" => []]],
        tags: ["Reviews"],
        parameters: [
            new OA\Parameter(
                name: "eventId",
                description: "Event ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "reviewId",
                description: "Review ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            ),
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Review deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string")
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized"),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: "Not the owner of the review"),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Event or review not found")
        ]
    )]
    public function __invoke(int $eventId, int $reviewId): Response
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $event = $this->eventRepository->find($eventId);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        $review = $this->reviewRepository->find($reviewId);
        if (!$review || $review->event_id !== $event->id) {
            return response()->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        if ($review->user_id !== $user->id) {
            return response()->json(['error' => 'You are not the owner of this review'], Response::HTTP_FORBIDDEN);
        }

        $deleted = $this->reviewRepository->delete($reviewId);
        if (!$deleted) {
            return response()->json(['error' => 'Failed to delete review'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Review deleted successfully'], Response::HTTP_OK);
    }
}
