<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\{
    BaseRepositoryInterface,
    EventRepositoryInterface,
    UserRepositoryInterface,
    ReservationRepositoryInterface,
    ReviewRepositoryInterface
};
use App\Repositories\Eloquent\{
    EloquentBaseRepository,
    EloquentEventRepository,
    EloquentUserRepository,
    EloquentReservationRepository,
    EloquentReviewRepository
};

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BaseRepositoryInterface::class, EloquentBaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EloquentEventRepository::class);
        $this->app->bind(ReservationRepositoryInterface::class, EloquentReservationRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, EloquentReviewRepository::class);
    }
}
