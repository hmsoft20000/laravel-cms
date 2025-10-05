<?php

namespace HMsoft\Cms\Repositories\Contracts;


interface PagesMetaRepositoryInterface extends BaseRepositoryInterface
{
  
    public function updateMultiple(array $pagesData): bool;
    public function refreshCache(): mixed;
}
