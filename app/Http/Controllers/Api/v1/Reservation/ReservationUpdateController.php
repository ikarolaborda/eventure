<?php

namespace App\Http\Controllers\Api\v1\Reservation;

use App\Repositories\ReservationRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

class ReservationUpdateController
{

    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    )
    {
    }


    #[OA\Put(
        path: "/api/v1/reservations/{id}",
        operationId: "updateReservation",
        summary: "Update a reservation (example, if you implement partial updates)",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(type: "object") // Adjust with fields if needed
        ),
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
            new OA\Response(response: Response::HTTP_OK, description: "Reservation updated"),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Reservation not found"),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]

    public function __invoke(int $id): Response
    {
        return response()->json([
        ], Response::HTTP_OK);
    }

}
