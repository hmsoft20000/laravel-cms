<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Repositories\Contracts\BaseRepositoryInterface;


interface SliderRepositoryInterface extends BaseRepositoryInterface
{
    public function proceedImage($file, string|null $oldFileName = null): bool|string;
    public function proceedImageDelete($file);
}
