<?php

namespace App\Http\Controllers\Api\v1\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Review\ReviewUpdateRequest;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReviewUpdateController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

    public function __invoke(ReviewUpdateRequest $request, int $eventId, int $reviewId): Response
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

        $updated = $this->reviewRepository->update($reviewId, $request->validated());
        if (!$updated) {
            return response()->json(['error' => 'Failed to update review'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $review->refresh();

        return response()->json(['message' => 'Review updated successfully', 'review' => $review], Response::HTTP_OK);
    }
}
