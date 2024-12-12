<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\Reservation;
use App\Models\User;

interface ReservationRepositoryInterface extends BaseRepositoryInterface
{
    public function createForUserAndEvent(User $user, Event $event): bool;
    public function userHasReservation(User $user, Event $event): bool;
    public function countEventReservations(Event $event): int;
    public function findForUser(User $user, int $id): ?object;
    public function allForUser(User $user): \Illuminate\Support\Collection;
}
