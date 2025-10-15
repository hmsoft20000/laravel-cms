<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Model;

class EloquentRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function update($model, array $data)
    {
        $model->update($data);
        return $model->fresh();
    }

    public function delete($model)
    {
        return $model->delete();
    }
}
