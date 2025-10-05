<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Lang;
use HMsoft\Cms\Repositories\Contracts\LangRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class LangRepository implements LangRepositoryInterface
{

    public function __construct(
        private readonly Lang $model,
    ) {}

    public function store(array $data): Model
    {
        $model = $this->model->create($data);
        return $this->show($model);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $this->show($model);
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        return true;
    }
}
