<?php

namespace App\Http\Controllers\Api\v1\Reservation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Reservation\ReservationStoreRequest;
use App\Repositories\EventRepositoryInterface;
use App\Repositories\ReservationRepositoryInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class ReservationStoreController extends Controller
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository,
        private readonly EventRepositoryInterface $eventRepository,
    )
    {
    }

    #[OA\Post(
        path: "/api/v1/events/{eventId}/reserve",
        operationId: "createReservation",
        summary: "Reserve a ticket for a given event",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(ref: "#/components/schemas/ReservationStoreRequest")
        ),
        tags: ["Reservations"],
        parameters: [
            new OA\Parameter(
                name: "eventId",
                description: "Event ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Reservation successful",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string")
                ])
            ),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Event not found"),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Booking deadline passed or event fully booked or already reserved"),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]
    public function __invoke(ReservationStoreRequest $request, int $eventId): Response
    {
        $event = $this->eventRepository->find($eventId);
        if (!$event) {
            return response()
                ->json(['error' => 'Event not found'],
                Response::HTTP_NOT_FOUND
                );
        }

        $now = now();
        if ($now->greaterThan($event->booking_deadline)) {
            return response()
                ->json(['error' => 'Booking deadline has passed'],
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }

        $currentReservations = $this->reservationRepository->countEventReservations($event);
        if ($currentReservations >= $event->attendee_limit) {
            return response()
                ->json(['error' => 'Event is fully booked'],
                Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }

        $user = auth()->user();
        if ($this->reservationRepository->userHasReservation($user, $event)) {
            return response()
                ->json(['error' => 'You have already reserved a ticket for this event'],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
        }

        $this->reservationRepository->createForUserAndEvent($user, $event);

        return response()->json(['message' => 'Reservation successful'], Response::HTTP_CREATED);
    }
}
