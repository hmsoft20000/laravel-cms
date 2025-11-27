<?php

namespace HMsoft\Cms\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemJoin extends Model
{
    protected $table = 'item_joins';
    public $timestamps = false; // لا يوجد timestamps في الـ SQL
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function parentItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'parent_item_id');
    }

    public function childItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'child_item_id');
    }
}
