<?php

namespace HMsoft\Cms\Repositories\Contracts;

interface ServiceRepositoryInterface extends BaseRepositoryInterface
{
    public function updateAll(array $servicesData): array;
}
