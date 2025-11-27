<?php

namespace HMsoft\Cms\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemAddonOption extends Model
{
    protected $table = 'item_addon_options';
    public $timestamps = false; // لا يوجد timestamps في الـ SQL
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_default' => 'boolean',
        'sort_number' => 'integer',
    ];

    public function addon(): BelongsTo
    {
        return $this->belongsTo(ItemAddon::class, 'item_addon_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ItemAddonOptionTranslation::class, 'item_addon_option_id');
    }
}
