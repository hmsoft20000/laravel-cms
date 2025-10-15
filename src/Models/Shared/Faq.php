<?php

namespace HMsoft\Cms\Models\Shared;

use HMsoft\Cms\Models\GeneralModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Polymorphic FAQ Model.
 * Can be attached to any model (e.g., Post, Product).
 */
class Faq extends GeneralModel
{
    /**
     * The table associated with the model.
     * @var string
     */
    protected $table = "faqs";

    /**
     * The attributes that aren't mass assignable.
     * @var array<string>|bool
     */
    protected $guarded = ['id'];


    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_number' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the parent owner model (Post, Product, etc.).
     */
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    /**
     * Get all of the translations for the FAQ.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(FaqTranslation::class, 'faq_id');
    }


    /**
     * Scope a query to only include faqs of a given type.
     */
    public function scopeOfType(Builder $query, string $type): void
    {
        $query->where('owner_type', $type);
    }

    public function defineRelationships(): array
    {
        return [
            'translations' => 'translations',
        ];
    }

    public function defineFieldSelectionMap(): array
    {
        $defaultMap = parent::defineFieldSelectionMap();

        $customMap = [
            'question' => 'translations.question',
        ];

        return array_merge($defaultMap, $customMap);
    }

    public function defineFilterableAttributes(): array
    {
        $baseColumns = parent::defineFilterableAttributes();

        $relatedAttributes = [
            'translations.question',
            'translations.answer',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineSortableAttributes(): array
    {
        $baseColumns = parent::defineSortableAttributes();

        $relatedAttributes = [
            'translations.question',
        ];

        return array_merge($baseColumns, $relatedAttributes);
    }

    public function defineGlobalSearchRelatedAttributes(): array
    {
        return [
            'translations' => ['question', 'answer'],
        ];
    }
}
