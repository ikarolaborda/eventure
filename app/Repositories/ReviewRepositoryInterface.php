<?php

namespace App\Repositories;

use App\Models\Event;
use App\Models\User;
use App\Models\Review;

interface ReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function createForEvent(User $user, Event $event, array $data): Review;
    public function getEventReviews(Event $event): \Illuminate\Support\Collection;
    public function getEventAverageRating(Event $event): float;
}
