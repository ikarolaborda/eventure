<?php

namespace App\Http\Controllers\Api\v1\Review;

use App\Http\Controllers\Controller;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewListController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

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
