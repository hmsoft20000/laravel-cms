<?php

namespace HMsoft\Cms\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemAddonOptionTranslation extends Model
{
    protected $table = 'item_addon_option_translations';
    public $timestamps = false;

    protected $fillable = [
        'item_addon_option_id',
        'locale',
        'title',
    ];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ItemAddonOption::class, 'item_addon_option_id');
    }
}
