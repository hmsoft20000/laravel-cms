<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\Content\Blog;
use Illuminate\Database\Eloquent\Model;

interface BlogRepositoryInterface extends BaseRepositoryInterface
{
    public function attachDownloads(Blog $blog, array $downloadItemIds): void;
}
