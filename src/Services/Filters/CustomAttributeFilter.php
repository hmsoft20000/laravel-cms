<?php

namespace HMsoft\Cms\Services\Filters;

use Illuminate\Database\Eloquent\Builder;
use HMsoft\Cms\Data\ColumnFilterData;
use HMsoft\Cms\Interfaces\AutoFilterable;
use HMsoft\Cms\Models\Shared\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CustomAttributeFilter
{
    public const ATTRIBUTE_PREFIX = 'attribute_';

    /**
     * Applies a filter for custom attributes using a WHERE EXISTS subquery.
     */
    public static function apply(Builder $query, Attribute $attribute, ColumnFilterData $filter, AutoFilterable|Model $model): void
    {

        $avTable = (new \HMsoft\Cms\Models\Shared\AttributeValue)->getTable();

        $mainTable = $model->getTable();
        $mainKey = $model->getKeyName();
        $mainMorphClass = $model->getMorphClass();

        // Add a subquery for this attribute filter
        $query->whereExists(function (Builder $subQuery) use ($mainTable, $mainKey, $mainMorphClass, $attribute, $filter, $avTable) {

            $subQuery->select(DB::raw(1))
                ->from("{$avTable} as av")
                ->whereColumn('av.owner_id', "{$mainTable}.{$mainKey}")
                ->where('av.owner_type', $mainMorphClass)
                ->where('av.attribute_id', $attribute->id);

            switch ($attribute->type) {
                case 'text':
                case 'number':
                case 'date':
                case 'textarea':
                    self::applySingleValueFilter($subQuery, $filter);
                    break;

                case 'select':
                case 'radio':
                case 'checkbox':
                case 'multi-select':
                    self::applyMultiValueFilter($subQuery, $filter);
                    break;
            }
        });
    }

    /**
     * Applies a single-value condition inside the EXISTS subquery.
     */
    protected static function applySingleValueFilter(Builder $subQuery, ColumnFilterData $filter): void
    {

        // Create a new DTO instance with the correct aliased column name.
        $tempFilterData = new ColumnFilterData(
            id: 'av.value', // The column inside the subquery
            value: $filter->value,
            filterFns: $filter->filterFns
        );

        // Let the DTO apply its own filtering logic to the subquery.
        $tempFilterData->buildQuery($subQuery);
    }

    /**
     * Applies a multi-value condition (e.g., select, checkbox, multi-select) inside the EXISTS subquery.
     */
    protected static function applyMultiValueFilter(Builder $subQuery, ColumnFilterData $filter): void
    {
        $asoTable = (new \HMsoft\Cms\Models\Shared\AttributeSelectedOption)->getTable();

        $selectedOptionIds = is_array($filter->value)
            ? $filter->value
            : explode(',', $filter->value);

        // Encapsulated join — affects only the subquery
        $subQuery->join("{$asoTable} as aso", 'aso.attribute_value_id', '=', 'av.id')
            ->whereIn('aso.attribute_option_id', $selectedOptionIds);
    }

    /**
     * Detect if the filter is for a custom attribute.
     */
    public static function isCustomAttribute(ColumnFilterData $filter): bool
    {
        return str_starts_with($filter->id, self::ATTRIBUTE_PREFIX);
    }
}
