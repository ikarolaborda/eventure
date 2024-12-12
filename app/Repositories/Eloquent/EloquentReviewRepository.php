<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Models\Review;
use App\Models\User;
use App\Repositories\ReviewRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentReviewRepository extends EloquentBaseRepository implements ReviewRepositoryInterface
{
    public function __construct(Review $model)
    {
        parent::__construct($model);
    }

    public function createForEvent(User $user, Event $event, array $data): Review
    {
        return $this->model->create([
            'user_id'  => $user->id,
            'event_id' => $event->id,
            'rating'   => $data['rating'],
            'comment'  => $data['comment'] ?? null,
        ]);
    }

    public function getEventReviews(Event $event): Collection
    {
        return $this->model
            ->where('event_id', $event->id)
            ->get();
    }

    public function getEventAverageRating(Event $event): float
    {
        $avg = $this->model
            ->where('event_id', $event->id)
            ->avg('rating');

        return $avg ?? 0.0;
    }
}
