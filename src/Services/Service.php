<?php

namespace Ryan\LineKit\Services;

use Ryan\LineKit\Repositories\Repository;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class Service
{
    protected $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll($columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    public function getList(array $attributes)
    {
        return $this->repository->paginate($attributes['perPage']);
    }

    public function firstOrCreate($attributes)
    {
        return $this->repository->firstOrCreate($attributes);
    }

    public function firstOrNew(array $attributes)
    {
        return $this->repository->firstOrNew($attributes);
    }

    public function create(array $attributes)
    {
        return $this->repository->create($attributes);
    }

    public function getSingleData($id)
    {
        return $this->repository->find($id);
    }

    public function update($id, array $attributes)
    {
        return $this->repository->update($id, $attributes);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }

    /**
    處理與請求的關係
    一次性判斷
     */
    protected function proccesRelationWithRequest(Relation $relation, array $items)
    {
        $relation->getResults()->each(function ($model) use ($items) {
            foreach ($items as $item) {
                if ($model->id === ($item['id'] ?? null)) {
                    $model->fill(($item))->save();
                    return;
                }
            }

            return $model->delete();
        });

        foreach ($items as $item) {
            if (!isset($item['id'])) {
                $relation->create(($item));
            }
        };
    }
}
