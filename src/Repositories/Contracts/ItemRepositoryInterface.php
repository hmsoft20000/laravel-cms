<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\Shop\Item; // <-- أضف هذا
use Illuminate\Database\Eloquent\Model; // <-- قد يكون هذا موجودًا بالفعل

interface ItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param Item $item
     * @param array $downloadItemIds
     * @return void
     */
    public function attachDownloads(Item $item, array $downloadItemIds): void;
}
