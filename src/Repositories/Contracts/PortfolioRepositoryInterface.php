<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\Content\Portfolio;
use Illuminate\Database\Eloquent\Model;

interface PortfolioRepositoryInterface extends BaseRepositoryInterface
{
    public function attachDownloads(Portfolio $portfolio, array $downloadItemIds): void;
}
