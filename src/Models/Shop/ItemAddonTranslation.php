<?php

namespace HMsoft\Cms\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemAddonTranslation extends Model
{
    protected $table = 'item_addon_translations';
    public $timestamps = false;

    protected $fillable = [
        'item_addon_id',
        'locale',
        'title',
    ];

    public function addon(): BelongsTo
    {
        return $this->belongsTo(ItemAddon::class, 'item_addon_id');
    }
}
