<?php

namespace HMsoft\Cms\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CategoryRepositoryInterface
{
    public function store(array $data): Model;
    public function update(Model $category, array $data): Model;
    public function show(Model $model): Model;
    public function delete(Model $model): bool;
    public function destroy(Model $model): bool;
}
