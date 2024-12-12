<?php

namespace App\Http\Controllers\Api\v1\Event;

use App\Http\Controllers\Controller;
use App\Repositories\EventRepositoryInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class EventShowController extends Controller
{
    public function __construct(
        private readonly EventRepositoryInterface $eventRepository
    ) {}

    #[OA\Get(
        path: "/api/v1/events/{id}",
        operationId: "showEvent",
        summary: "Show a single event",
        tags: ["Events"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Event ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Event details",
                content: new OA\JsonContent(type: "object")
            ),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: "Event not found")
        ]
    )]
    public function __invoke(Request $request, int $id): Response
    {
        $event = $this->eventRepository->find($id);
        if (!$event) {
            return response()->json(['error' => 'Event not found'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($event);
    }
}
