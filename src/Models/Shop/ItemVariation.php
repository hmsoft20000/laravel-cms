<?php

namespace HMsoft\Cms\Models\Shop;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use HMsoft\Cms\Models\Shared\AttributeOption; //
use HMsoft\Cms\Traits\Media\HasMedia;
use HMsoft\Cms\Traits\General\FileManagerTrait;

class ItemVariation extends GeneralModel
{

    use HasMedia;
    use FileManagerTrait;

    protected $table = 'item_variations';
    public $timestamps = false; // لا يوجد timestamps في الـ SQL
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function getMorphClass()
    {
        return 'item_variation';
    }

    /**
     * جلب خيارات الخصائص (مثل: أحمر، كبير) التي تكون هذا التوليف
     * هذا يستبدل الحاجة إلى موديل 'ItemVariationOption'
     */
    public function attributeOptions(): BelongsToMany
    {
        return $this->belongsToMany(
            AttributeOption::class,
            'item_variation_options',    // اسم جدول الربط
            'item_variation_id',         // المفتاح الخاص بهذا الموديل
            'attribute_option_id'      // المفتاح الخاص بالموديل الآخر
        );
    }
}
