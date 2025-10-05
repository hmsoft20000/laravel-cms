<?php

namespace HMsoft\Cms\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BusinessSettingRepositoryInterface
{
    public function find(string $id): ?Model;
    public function all(): array;
    public function update(string $id, array $data): bool;
    public function updateAll(array $data);
    public function proceedImage($key, $file, string|null $oldFileName = null): bool|string;
    public function proceedImageDelete($key, $file);
    public static function getImageFolder($key);

    /** 
     * upload image or delete it.
     * @return string|null image  url or null
     */
    public function handelImage($key, $image, $deleteImage): string|null;
    public function refreshCache(): mixed;
}
