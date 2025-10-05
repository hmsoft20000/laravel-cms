<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Polymorphic AttributeValue Model.
 *
 * This model stores the actual value of an attribute for a specific owner model (e.g., Post, Product).
 */
class AttributeValue extends GeneralModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "attribute_values";

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    protected function casts(): array
    {
        return [
            'value' => \HMsoft\Cms\Casts\DynamicValueCast::class,
        ];
    }

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the parent owner model (Post, Product, etc.).
     * The name 'owner' tells Laravel to look for 'owner_id' and 'owner_type' columns.
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    /**
     * Get the attribute definition that this value belongs to.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * For checkbox type attributes, get the selected options.
     */
    public function selectedOptions(): HasMany
    {
        return $this->hasMany(AttributeSelectedOption::class, 'attribute_value_id');
    }
}
