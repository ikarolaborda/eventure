<?php

namespace App\Http\Controllers\Api\v1\Review;

use App\Http\Controllers\Controller;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ReviewListController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}


    #[OA\Get(
        path: "/api/v1/events/{id}/reviews",
        operationId: "listEventReviews",
        summary: "List all reviews for a specific event",
        tags: ["Reviews"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Event ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "A list of reviews with average rating",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "reviews", type: "array", items: new OA\Items(type: "object")),
                        new OA\Property(property: "average_rating", type: "number", format: "float")
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Event not found")
        ]
    )]
    public function __invoke(Request $request, int $eventId): Response
    {
        $event = $this->eventRepository->find($eventId);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }

        $reviews = $this->reviewRepository->getEventReviews($event);
        $average = $this->reviewRepository->getEventAverageRating($event);

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => $average
        ], Response::HTTP_OK);
    }
}
