<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Models\Shop\ItemVariation;
use Illuminate\Database\Eloquent\Model;

interface ItemVariationRepositoryInterface
{
    public function store(Item $item, array $data): Model;

    public function update(ItemVariation $variation, array $data): Model;

    public function delete(ItemVariation $variation): bool;
}
