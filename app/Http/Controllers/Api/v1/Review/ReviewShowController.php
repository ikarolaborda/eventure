<?php

namespace App\Http\Controllers\Api\v1\Review;

use App\Http\Controllers\Controller;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ReviewShowController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}


    #[OA\Get(
        path: "/api/v1/events/{eventId}/reviews/{reviewId}",
        operationId: "showReview",
        summary: "Show a single review for a given event",
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
                description: "Review details",
                content: new OA\JsonContent(type: "object")
            ),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Event or review not found")
        ]
    )]
    public function __invoke(Request $request, int $eventId, int $reviewId): Response
    {
        $event = $this->eventRepository->find($eventId);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        $review = $this->reviewRepository->find($reviewId);
        if (!$review || $review->event_id !== $event->id) {
            return response()->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($review, Response::HTTP_OK);
    }
}
