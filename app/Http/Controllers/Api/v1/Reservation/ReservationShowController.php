<?php

namespace App\Http\Controllers\Api\v1\Reservation;

use App\Http\Controllers\Controller;
use App\Repositories\ReservationRepositoryInterface;
use OpenApi\Attributes as OA;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReservationShowController extends Controller
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservationRepository
    ) {}

    #[OA\Get(
        path: "/api/v1/reservations/{id}",
        operationId: "showReservation",
        summary: "Show a single reservation for the authenticated user",
        security: [["bearerAuth" => []]],
        tags: ["Reservations"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Reservation ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Reservation details",
                content: new OA\JsonContent(type: "object")
            ),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Reservation not found"),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]
    public function __invoke(Request $request, int $id)
    {
        $user = auth()->user();
        $reservation = $this->reservationRepository->findForUser($user, $id);

        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        return response()->json($reservation);
    }
}
