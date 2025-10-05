<?php

namespace HMsoft\Cms\Repositories\Contracts;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function updateAll(array $postsData): array;
}
