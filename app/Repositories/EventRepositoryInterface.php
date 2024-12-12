<?php

namespace App\Repositories;

use App\Models\Event;
use Illuminate\Support\Collection;

interface EventRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUser(int $userId): Collection;
}
