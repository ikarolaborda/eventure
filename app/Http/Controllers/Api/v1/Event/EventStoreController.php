<?php

namespace App\Http\Controllers\Api\v1\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\Event\EventStoreRequest;
use App\Repositories\EventRepositoryInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class EventStoreController extends Controller
{

    public function __construct(
        private readonly EventRepositoryInterface $eventRepository
    ) {}

    #[OA\Post(
        path: "/api/v1/events",
        operationId: "createEvent",
        summary: "Create a new event",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/EventStoreRequest")
        ),
        tags: ["Events"],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: "Event created",
                content: new OA\JsonContent(type: "object")
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized"),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error")
        ]
    )]
    public function __invoke(EventStoreRequest $request): Response
    {
        if(!auth()->user()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $event = $this->eventRepository->create(array_merge($request->validated(), ['user_id' => auth()->user()->id]));

        return response()->json($event, Response::HTTP_CREATED);
    }

}
