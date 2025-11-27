<?php

namespace HMsoft\Cms\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemTranslation extends Model
{
    protected $table = 'item_translations';
    public $timestamps = false;
    protected $fillable = [
        'item_id',
        'locale',
        'title',
        'short_content',
        'content',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
