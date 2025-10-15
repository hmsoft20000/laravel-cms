<?php

namespace HMsoft\Cms\Repositories\Contracts;

interface PortfolioRepositoryInterface extends BaseRepositoryInterface
{
    public function updateAll(array $portfoliosData): array;
}
