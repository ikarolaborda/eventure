<?php

namespace App\Http\Controllers\Api\v1\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Review\ReviewStoreRequest;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReservationRepositoryInterface;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReviewStoreController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository,
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly ReviewRepositoryInterface $reviewRepository
    ) {}

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
