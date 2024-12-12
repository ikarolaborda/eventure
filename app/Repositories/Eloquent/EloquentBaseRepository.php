<?php

namespace App\Repositories\Eloquent;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentBaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function find(int $id): ?object
    {
        return $this->model->find($id);
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function create(array | object $data): object
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $instance = $this->model->find($id);
        if (!$instance) {
            return false;
        }
        return $instance->update($data);
    }

    public function delete(int $id): bool
    {
        $instance = $this->model->find($id);
        if (!$instance) {
            return false;
        }
        return (bool) $instance->delete();
    }
}
