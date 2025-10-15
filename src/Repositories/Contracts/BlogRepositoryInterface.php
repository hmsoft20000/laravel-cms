<?php

namespace HMsoft\Cms\Repositories\Contracts;

interface BlogRepositoryInterface extends BaseRepositoryInterface
{
    public function updateAll(array $blogsData): array;
}
