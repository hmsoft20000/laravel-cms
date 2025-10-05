<?php

namespace HMsoft\Cms\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use HMsoft\Cms\Repositories\Contracts\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function updatePassword(Model $user, string $newPassword): bool;

    public function updateInfo(Model $user, array $data): bool;

    public function checkOldPassword(Model $user, string $oldPassword): bool;

    public function proceedImage($file, string|null $oldFileName = null): bool|string;

    public function proceedImageDelete($file);
}
