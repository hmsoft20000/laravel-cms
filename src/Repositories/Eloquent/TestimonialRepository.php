<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Testimonial\Testimonial;
use HMsoft\Cms\Repositories\Contracts\TestimonialRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class TestimonialRepository implements TestimonialRepositoryInterface
{

    public function __construct(
        private readonly Testimonial $model
    ) {}

    public function store(array $data): Model
    {
        $createData = Arr::except($data, ['locales']);
        $createdModel = $this->model->create($createData);
        return $createdModel;
    }


    public function show(Model $model): Model
    {
        return $model;
    }


    public function update(Model $model, array $data): Model
    {
        $updateData = Arr::except($data, ['locales']);
        $model->update($updateData);
        $model->refresh();
        return  $this->show($model);
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
