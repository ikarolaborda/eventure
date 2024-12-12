<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function find(int $id): ?object;
    public function all(): Collection;
    public function create(array | object $data): object;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
