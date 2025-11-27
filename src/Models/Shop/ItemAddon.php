<?php

namespace HMsoft\Cms\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemAddon extends Model
{
    protected $table = 'item_addons';
    public $timestamps = false; // لا يوجد timestamps في الـ SQL
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_required' => 'boolean',
        'sort_number' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ItemAddonTranslation::class, 'item_addon_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ItemAddonOption::class, 'item_addon_id');
    }
}
