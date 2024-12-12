<?php

namespace App\Http\Controllers\Api\v1\Reservation;

use App\Repositories\ReservationRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class ReservationDestroyController
{
    public function __construct(
        private ReservationRepositoryInterface $reservationRepository
    )
    {
    }

    public function __invoke(int $id): Response
    {
        $this->reservationRepository->delete($id);
        return response()->json([
        ], Response::HTTP_NO_CONTENT);
    }

}
