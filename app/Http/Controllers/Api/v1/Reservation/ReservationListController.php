<?php

namespace App\Http\Controllers\Api\v1\Reservation;

use App\Repositories\ReservationRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

readonly class ReservationListController
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    )
    {
    }

    #[OA\Get(
        path: "/api/v1/reservations",
        operationId: "listReservations",
        summary: "List all reservations for the authenticated user",
        security: [["bearerAuth" => []]],
        tags: ["Reservations"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "List of reservations",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]
    public function __invoke(): Response
    {
        return response()->json([
            $this->reservationRepository->all()
        ], Response::HTTP_OK);
    }

}
