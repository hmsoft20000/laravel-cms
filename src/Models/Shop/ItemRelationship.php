<?php

namespace HMsoft\Cms\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemRelationship extends Model
{
    protected $table = 'item_relationships';
    public $timestamps = false; // لا يوجد timestamps في الـ SQL
    protected $guarded = ['id'];

    protected $casts = [
        'sort_number' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function relatedItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'related_item_id');
    }
}
