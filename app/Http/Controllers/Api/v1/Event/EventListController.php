<?php

namespace App\Http\Controllers\Api\v1\Event;

use App\Http\Controllers\Controller;
use App\Repositories\EventRepositoryInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class EventListController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository
    ) {}

    #[OA\Get(
        path: "/api/v1/events",
        operationId: "listEvents",
        summary: "List all events",
        tags: ["Events"],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "List of events",
                content: new OA\JsonContent(type: "array", items: new OA\Items(type: "object"))
            )
        ]
    )]
    public function __invoke()
    {
        $events = $this->eventRepository->all();
        return response()->json($events);
    }
}
