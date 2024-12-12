<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\User;
use App\Repositories\ReservationRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentReservationRepository extends EloquentBaseRepository implements ReservationRepositoryInterface
{
    public function __construct(Reservation $model)
    {
        parent::__construct($model);
    }

    public function createForUserAndEvent(User $user, Event $event): bool
    {
        $reservation = $this->model->create([
            'user_id' => $user->id,
            'event_id' => $event->id
        ]);

        return $reservation instanceof Reservation;
    }

    public function userHasReservation(User $user, Event $event): bool
    {
        return $this->model
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->exists();
    }

    public function countEventReservations(Event $event): int
    {
        return $this->model->where('event_id', $event->id)->count();
    }

    public function findForUser(User $user, int $id): ?object
    {
        return $this->model
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->first();
    }

    public function allForUser(User $user): Collection
    {
        return $this->model
            ->where('user_id', $user->id)
            ->get();
    }
}
