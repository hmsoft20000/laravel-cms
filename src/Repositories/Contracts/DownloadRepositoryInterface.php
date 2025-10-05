<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Data\DynamicFilterData;
use Illuminate\Database\Eloquent\Model;

interface DownloadRepositoryInterface
{
    public function store(array $data): Model;
    public function update(Model $download, array $data): Model;
    public function show(Model $model): Model;
    public function delete(Model $model): bool;
}
