<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\Content\Service;
use Illuminate\Database\Eloquent\Model;

interface ServiceRepositoryInterface extends BaseRepositoryInterface
{
    public function attachDownloads(Service $service, array $downloadItemIds): void;
}
