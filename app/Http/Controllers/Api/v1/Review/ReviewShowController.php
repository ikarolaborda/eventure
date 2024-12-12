<?php

namespace App\Http\Controllers\Api\v1\Review;

use App\Http\Controllers\Controller;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewShowController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

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
