<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Repositories\EventRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentEventRepository extends EloquentBaseRepository implements EventRepositoryInterface
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function findByUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
