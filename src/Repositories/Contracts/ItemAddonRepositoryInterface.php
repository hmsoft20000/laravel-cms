<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Models\Shop\ItemAddon;
use Illuminate\Database\Eloquent\Model;

interface ItemAddonRepositoryInterface
{
    public function store(Item $item, array $data): Model;

    public function update(ItemAddon $addon, array $data): Model;

    public function delete(ItemAddon $addon): bool;
}
