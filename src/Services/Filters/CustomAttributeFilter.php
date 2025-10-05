<?php

namespace HMsoft\Cms\Services\Filters;

use Illuminate\Database\Eloquent\Builder;
use HMsoft\Cms\Data\ColumnFilterData;
use HMsoft\Cms\Enums\FilterFnsEnum;
use HMsoft\Cms\Interfaces\AutoFilterable;

use HMsoft\Cms\Models\Shared\Attribute;
use Illuminate\Database\Eloquent\Model;

class CustomAttributeFilter
{
    /**
     * @var string The prefix used to identify custom attribute filters.
     */
    public const ATTRIBUTE_PREFIX = 'attribute_';

    /**
     * Applies a filter for custom attributes to the query builder.
     *
     * @param Builder $query The query builder instance.
     * @param ColumnFilterData $filter The filter data object.
     * @param AutoFilterable|Model $model The model instance implementing AutoFilterable.
     */
    public static function apply(Builder $query, ColumnFilterData $filter, AutoFilterable|Model $model): void
    {
        $attributeId = (int) str_replace(self::ATTRIBUTE_PREFIX, '', $filter->id);
        $attribute = Attribute::find($attributeId);

        if (!$attribute) {
            return;
        }

        // Generate a unique alias for the join to avoid conflicts
        $alias = "av_attribute_{$attributeId}";

        // Add the join to the attribute_values table
        $query->join("attribute_values as {$alias}", function ($join) use ($alias, $model, $attributeId) {
            $join->on("{$alias}.owner_id", '=', "{$model->getTable()}.{$model->getKeyName()}")
                ->where("{$alias}.owner_type", $model->getMorphClass())
                ->where("{$alias}.attribute_id", $attributeId);
        });

        switch ($attribute->type) {
            case 'text':
            case 'number':
            case 'date':
            case 'textarea':
                self::applySingleValueFilter($query, $alias, $filter);
                break;
            case 'select':
            case 'radio':
            case 'checkbox':
            case 'multi-select':
                self::applyMultiValueFilter($query, $alias, $attribute, $filter);
                break;
            default:
                break;
        }
    }

    /**
     * Applies a filter for single-value attributes using the joined table.
     */
    protected static function applySingleValueFilter(Builder $query, string $alias, ColumnFilterData $filter): void
    {
        $column = "{$alias}.value";
        switch ($filter->filterFns) {
            case FilterFnsEnum::equals:
                $query->where($column, $filter->value);
                break;
            case FilterFnsEnum::between:
                $values = is_array($filter->value) ? $filter->value : explode(',', $filter->value);
                if (count($values) === 2) {
                    $query->whereBetween($column, [$values[0], $values[1]]);
                }
                break;
            case FilterFnsEnum::in:
                $values = is_array($filter->value) ? $filter->value : explode(',', $filter->value);
                $query->whereIn($column, $values);
                break;
            case FilterFnsEnum::contains:
                $query->where($column, 'LIKE', '%' . $filter->value . '%');
                break;
        }
    }

    /**
     * Applies a filter for multi-value attributes using the joined table.
     */
    protected static function applyMultiValueFilter(Builder $query, string $alias, Attribute $attribute, ColumnFilterData $filter): void
    {
        $selectedOptionIds = is_array($filter->value) ? $filter->value : explode(',', $filter->value);
        $optionsAlias = "aso_attribute_{$attribute->id}";

        $query->join("attribute_selected_options as {$optionsAlias}", function ($join) use ($alias, $optionsAlias) {
            $join->on("{$optionsAlias}.attribute_value_id", '=', "{$alias}.id");
        });

        $query->whereIn("{$optionsAlias}.attribute_option_id", $selectedOptionIds);
    }

    /**
     * Determines if a filter is for a custom attribute.
     * @param ColumnFilterData $filter The filter data object.
     * @return bool
     */
    public static function isCustomAttribute(ColumnFilterData $filter): bool
    {
        return str_starts_with($filter->id, self::ATTRIBUTE_PREFIX);
    }
}
