<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Data\DynamicFilterData;
use Illuminate\Database\Eloquent\Model;

interface FaqRepositoryInterface
{
    public function store(array $data): Model;
    public function update(Model $faq, array $data): Model;
    public function show(Model $model): Model;
    public function delete(Model $model): bool;
}
