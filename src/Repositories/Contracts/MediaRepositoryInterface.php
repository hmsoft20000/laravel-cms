<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\Shared\Medium;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface MediaRepositoryInterface
{
    // Media Management - Generic interface
    public function store(Model $owner, array $data): Collection;
    public function update(Model $owner, Medium $medium, array $data): Medium;
    public function delete(Model $owner, int $mediaId): void;
    public function updateAll(Model $owner, array $mediaData): Collection;
    public function show(Model $owner, $medium): Model;
}
