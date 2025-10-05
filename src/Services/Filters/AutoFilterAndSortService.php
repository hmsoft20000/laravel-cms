<?php

namespace HMsoft\Cms\Services\Filters;

use HMsoft\Cms\Data\ColumnFilterData;
use HMsoft\Cms\Data\ColumnSortData;
use HMsoft\Cms\Data\DynamicFilterData;
use HMsoft\Cms\Enums\FilterFnsEnum;
use HMsoft\Cms\Enums\PaginationFormateEnum;
use HMsoft\Cms\Interfaces\AutoFilterable;
use HMsoft\Cms\Services\Filters\CustomAttributeFilter;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class AutoFilterAndSortService
{

    private JoinManager $joinManager;

    public function __construct(
        public string|Model $model
    ) {
        if (is_string($this->model)) {
            if (!class_exists($this->model)) {
                throw new \Exception("Class {$this->model} does not exist.");
            }

            $modelInstance = new $this->model();
            if (!$modelInstance instanceof Model) {
                throw new \Exception("Class {$this->model} must be an instance of Illuminate\Database\Eloquent\Model.");
            }

            $this->model = $modelInstance;
        } elseif (!$this->model instanceof Model) {
            throw new \Exception("Model must be an instance of Illuminate\Database\Eloquent\Model or a class name.");
        }
    }

    public function initializeDynamicFilterData(
        ?DynamicFilterData $dynamicFilterData = null,
        ?Request $request = null,
        $page = null,
        $perPage = null,
        $paginationFormate = null,
        $filters = null,
        $sorting = null,
        $globalFilter = null,
        $advanceFilter = null,
        $globaleFilterExtraOperation = null,
        $extraOperation = null,
        $beforeOperation = null,
        array $filterKeyMap = [],
        array $sortKeyMap = [],
        $fields = null
    ): DynamicFilterData {
        if ($dynamicFilterData) {
            return $dynamicFilterData;
        }

        $request = $request ?? request();

        $finalPage = $page ?? $request->input('page');
        $finalPerPage = $perPage ?? $request->input('perPage', $request->input('per_page', $request->input('limit')));

        $finalPaginationFormate = is_null($paginationFormate)
            ? PaginationFormateEnum::from($request->input('paginationFormate', PaginationFormateEnum::separated->value))
            : $paginationFormate;

        if (is_null($finalPage) || is_null($finalPerPage) || $finalPerPage === 'all' || $request->header('pdt') === '0') {
            $finalPaginationFormate = PaginationFormateEnum::none;
            $finalPage = 'all';
            $finalPerPage = 'all';
        }

        $finalFilters = $filters ?? self::getFiltersValuesFromRequest($request);
        $finalSorting = $sorting ?? self::getSortingValuesFromRequest($request);
        $finalAdvanceFilter = $advanceFilter ?? self::getAdvanceFilterFromRequest($request);
        $finalGlobalFilter = $globalFilter ?? $request->input('globalFilter');
        $finalFields = $fields ?? $request->input('fields');

        if (!empty($filterKeyMap)) {
            $finalFilters = $finalFilters->map(function ($filter) use ($filterKeyMap) {
                if (isset($filterKeyMap[$filter->id])) {
                    $filter->id = $filterKeyMap[$filter->id];
                }
                return $filter;
            });
        }

        if (!empty($sortKeyMap)) {
            $finalSorting = $finalSorting->map(function ($sort) use ($sortKeyMap) {
                if (isset($sortKeyMap[$sort->id])) {
                    $sort->id = $sortKeyMap[$sort->id];
                }
                return $sort;
            });
        }

        return new DynamicFilterData(
            page: $finalPage,
            perPage: $finalPerPage,
            paginationFormate: $finalPaginationFormate,
            filters: $finalFilters,
            advanceFilter: $finalAdvanceFilter,
            sorting: $finalSorting,
            globalFilter: $finalGlobalFilter,
            globaleFilterExtraOperation: $globaleFilterExtraOperation,
            extraOperation: $extraOperation,
            beforeOperation: $beforeOperation,
            fields: $finalFields
        );
    }

    public function buildQuery(?DynamicFilterData $dynamicFilterData = null): Builder
    {
        if (!($this->model instanceof AutoFilterable)) {
            throw new \Exception('Model ' . get_class($this->model) . ' must implement the AutoFilterable interface.');
        }

        if (!$dynamicFilterData) {
            $dynamicFilterData = $this->initializeDynamicFilterData();
        }

        $query = $this->model->query();
        $mainTableAlias = 't_main'; // The alias for the main table.
        $query->from($this->model->getTable(), $mainTableAlias); // Apply the alias immediately.


        $this->joinManager = new JoinManager($query, $mainTableAlias);

        // 1. Build dynamic SELECT clause (now alias-aware).
        $this->buildSelectClause($query, $dynamicFilterData->fields);

        $extraOperation = $dynamicFilterData->extraOperation;
        $globaleFilterExtraOperation = $dynamicFilterData->globaleFilterExtraOperation;
        $beforeOperation = $dynamicFilterData->beforeOperation;

        // 2. Whitelisting Logic
        $allowedFilters = $this->model->defineFilterableAttributes();
        $allowedSorts = $this->model->defineSortableAttributes();

        $dynamicFilterData->filters = collect($dynamicFilterData->filters)
            ->filter(fn(ColumnFilterData $filter) => in_array(explode('.', $filter->id)[0], $allowedFilters))
            ->values();

        $dynamicFilterData->sorting = collect($dynamicFilterData->sorting)
            ->filter(fn(ColumnSortData $sort) => in_array(explode('.', $sort->id)[0], $allowedSorts))
            ->values();

        $pFilterKeys = collect($dynamicFilterData->filters)->groupBy('id');
        $sortingKeys = collect($dynamicFilterData->sorting)->groupBy('id');

        // 3. Apply "before" hook for any initial query modifications.
        if (isset($beforeOperation)) {
            $beforeOperation($query, ['filterKeys' => $pFilterKeys, 'sortingKeys' => $sortingKeys]);
        }

        // 4. Apply Filters (Advanced vs. Simple), now alias-aware.
        if (!empty($dynamicFilterData->advanceFilter)) {
            $query->where(function (Builder $builder) use ($dynamicFilterData, $allowedFilters) {
                self::applyAdvancedFilterGroup($builder, $dynamicFilterData->advanceFilter, $allowedFilters, $this->joinManager);
            });
        } else {
            $joinedTables = [];
            foreach ($dynamicFilterData->filters as $filter) {
                if (CustomAttributeFilter::isCustomAttribute($filter)) {
                    // $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id);
                    // $alias = "av_attribute_{$attributeId}";
                    // if (!isset($joinedTables[$alias])) {
                    //     CustomAttributeFilter::apply($query, $filter, $this->model);
                    //     $joinedTables[$alias] = true;
                    // }
                    // Custom attribute logic needs to be aware of the main table alias.
                    CustomAttributeFilter::apply($query, $filter, $this->model, $mainTableAlias);
                } else {
                    if (isset($pFilterKeys[$filter->id])) {
                        self::handelFilterOne($query, collect($pFilterKeys[$filter->id])->toArray(), $filter->id, $this->joinManager);
                    }
                }
            }
        }

        // 5. Apply Global Filter (now alias-aware).
        if (isset($dynamicFilterData->globalFilter) && !empty($dynamicFilterData->globalFilter)) {
            $this->applyGlobalFilter($query, $dynamicFilterData->globalFilter, $globaleFilterExtraOperation, $pFilterKeys, $sortingKeys);
        }

        // 6. Apply the "extra" hook for any final query modifications.
        if (isset($extraOperation)) {
            $extraOperation($query, ['filterKeys' => $pFilterKeys, 'sortingKeys' => $sortingKeys, 'globalFilter' => $dynamicFilterData->globalFilter]);
        }

        // 7. Apply Sorting (now alias-aware).
        self::handelSorting($query, $sortingKeys, $this->joinManager);

        // 8. Group By using the main table alias to ensure distinct results.
        $query->groupBy("{$mainTableAlias}." . $this->model->definePrimaryKeyName());

        return $query;
    }

    /**
     * Applies the global filter using aliases.
     */
    private function applyGlobalFilter(Builder $query, string $globalFilterValue): void
    {
        $mainTableAlias = $this->joinManager->getMainTableAlias();

        $query->where(function (Builder $builder) use ($globalFilterValue, $mainTableAlias) {
            // Search base attributes
            foreach ($this->model->defineGlobalSearchBaseAttributes() as $column) {
                $builder->orWhere("{$mainTableAlias}.{$column}", 'LIKE', "%{$globalFilterValue}%");
            }

            // Search translation attributes
            $relationships = $this->model->defineRelationships();
            if ($this->model->defineTranslationTableName() && isset($relationships['translations'])) {
                $translationAlias = $this->joinManager->ensureJoin($relationships['translations']);
                foreach ($this->model->defineGlobalSearchTranslationAttributes() as $column) {
                    $builder->orWhere("{$translationAlias}.{$column}", 'LIKE', "%{$globalFilterValue}%");
                }
            }
        });
    }

    /**
     * Get data handler with dynamic options, now smarter with AutoFilterable interface.
     *
     * @param DynamicFilterData $dynamicFilterData
     * @return array
     * @throws \Exception
     */
    public function dynamicFilter(DynamicFilterData $dynamicFilterData): array
    {

        $query = $this->buildQuery($dynamicFilterData);

        Log::info($query->toRawSql());

        $countQuery = clone $query;
        $countQuery->getQuery()->orders = null;
        $countQuery->getQuery()->columns = null;
        $countQuery->select(DB::raw('1'));
        $totalRecords = DB::connection($this->model->getConnectionName())->table(DB::raw("({$countQuery->toSql()}) as sub"))->mergeBindings($countQuery->getQuery())->count();
        $paginationData = $this->handelPageAndPerPage($dynamicFilterData->page, $dynamicFilterData->perPage, $totalRecords);
        $finalResult = $this->handelResultFormate($dynamicFilterData->paginationFormate, $paginationData['page'], $paginationData['perPage'], $query);
        return $finalResult;
    }

    /**
     * Recursively applies a group of advanced filter rules, now using the JoinManager.
     */
    private static function applyAdvancedFilterGroup(Builder $query, object $filterGroup, array $allowedFilters, JoinManager $joinManager): void
    {
        $condition = strtoupper($filterGroup->condition ?? 'AND') === 'OR' ? 'orWhere' : 'where';

        foreach ($filterGroup->rules ?? [] as $rule) {
            if (isset($rule->condition)) {
                // If the rule is another group, recurse into it.
                $query->{$condition}(function (Builder $subQuery) use ($rule, $allowedFilters, $joinManager) {
                    self::applyAdvancedFilterGroup($subQuery, $rule, $allowedFilters, $joinManager);
                });
            } elseif (isset($rule->id)) {
                // If it's a simple rule, apply it.
                if (!in_array($rule->id, $allowedFilters)) {
                    continue; // Security check
                }
                $whereClause = $condition === 'orWhere' ? 'OR' : 'AND';
                self::applySimpleFilterRule($query, $rule, $whereClause, $joinManager);
            }
        }
    }


    /**
     * Helper method to apply a single filter rule from an advanced filter group, now using the JoinManager.
     */
    private static function applySimpleFilterRule(Builder $query, object $rule, string $conditionType, JoinManager $joinManager): void
    {
        /** @var Model|AutoFilterable $model */
        $model = $query->getModel();
        $relationships = $model->defineRelationships();
        $columnId = $rule->id;

        $qualifiedColumnId = '';

        // Check if the filter ID is a relationship (e.g., 'translations.title')
        if (str_contains($columnId, '.')) {
            [$relationName, $columnName] = explode('.', $columnId, 2);

            // Check if the relationship is defined and allowed in the model
            if (isset($relationships[$relationName])) {
                $relationMethod = $relationships[$relationName];
                // Use the JoinManager to get the alias for the joined table
                $alias = $joinManager->ensureJoin($relationMethod);
                $qualifiedColumnId = "{$alias}.{$columnName}";
            }
        } else {
            // It's a column on the main table, so use the main table's alias
            $mainTableAlias = $joinManager->getMainTableAlias();
            $qualifiedColumnId = "{$mainTableAlias}.{$columnId}";
        }

        // If the column could not be resolved, ignore the filter.
        if (empty($qualifiedColumnId)) {
            return;
        }

        // Create a temporary DTO with the fully qualified and aliased column name
        $filterData = new ColumnFilterData(
            id: $qualifiedColumnId,
            value: $rule->value,
            filterFns: FilterFnsEnum::from($rule->filterFns),
        );

        // Use the DTO's existing logic to build the WHERE clause
        $filterData->buildQueryWhereStatment($query, $filterData, null, false, $conditionType);
    }

    public static function handelPageAndPerPage($page, $perPage, $totalCount)
    {
        $result['page'] = $page;
        $result['perPage'] = $perPage;
        if ($perPage == 'all' || $page == 'all') {
            $result['perPage'] = $totalCount > 0 ? $totalCount : 1;
            $result['page'] = 1;
        }
        return $result;
    }

    /**
     * Applies a collection of simple filters (for backward compatibility).
     */
    public static function handelFilter(&$query, $filterKeys, $columnPrefix = null)
    {



        $filterKeys->map(function ($filterValueObject, $columnId) use (&$query, $columnPrefix) {
            self::handelFilterOne($query, $filterValueObject, $columnId, $columnPrefix);
        });
    }

    /**
     * Applies a single simple filter using aliases.
     */
    public static function handelFilterOne(Builder $query, array $filterObjects, string $columnId, JoinManager $joinManager): void
    {
        $model = $query->getModel();
        $relationships = $model->defineRelationships();
        $filterData = $filterObjects[0];

        $qualifiedColumnId = '';
        if (str_contains($columnId, '.')) {
            [$relationName, $columnName] = explode('.', $columnId, 2);
            if (isset($relationships[$relationName])) {
                $relationMethod = $relationships[$relationName];
                $alias = $joinManager->ensureJoin($relationMethod);
                $qualifiedColumnId = "{$alias}.{$columnName}";
            }
        } else {
            $mainTableAlias = $joinManager->getMainTableAlias();
            $qualifiedColumnId = "{$mainTableAlias}.{$columnId}";
        }

        if (empty($qualifiedColumnId)) return; // Ignore if column is not valid

        // Use a temporary ColumnFilterData with the fully qualified (aliased) column name
        $value = is_array($filterData) ? $filterData['value'] : $filterData->value;
        $filterFns = is_array($filterData) ? $filterData['filterFns'] : $filterData->filterFns;
        $filterFnsEnum = is_string($filterFns) ? FilterFnsEnum::from($filterFns) : $filterFns;
        $tempFilterData = new ColumnFilterData($qualifiedColumnId, $value, $filterFnsEnum);
        $tempFilterData->buildQuery($query);
    }


    /**
     * Applies sorting using aliases.
     */
    public static function handelSorting(Builder $query, $sortedColumns, JoinManager $joinManager): void
    {
        $model = $query->getModel();
        $relationships = $model->defineRelationships();

        foreach ($sortedColumns as $columnCollection) {
            $sortingValue = $columnCollection[0];
            $columnId = $sortingValue->id;

            $qualifiedColumnId = '';
            if (str_contains($columnId, '.')) {
                [$relationName, $columnName] = explode('.', $columnId, 2);
                if (isset($relationships[$relationName])) {
                    $relationMethod = $relationships[$relationName];
                    $alias = $joinManager->ensureJoin($relationMethod);
                    $qualifiedColumnId = "{$alias}.{$columnName}";
                }
            } else {
                $mainTableAlias = $joinManager->getMainTableAlias();
                $qualifiedColumnId = "{$mainTableAlias}.{$columnId}";
            }

            if (empty($qualifiedColumnId)) continue; // Ignore if column is not valid

            $sortData = new ColumnSortData($qualifiedColumnId, $sortingValue->desc);
            $sortData->buildQuery($query);
        }
    }

    public static function handelResultFormate(
        PaginationFormateEnum $paginationFormate,
        $page,
        $perPage,
        Builder|\Illuminate\Database\Query\Builder &$query
    ): array {
        $finalResult = [
            'data' => null,
            'pagination' => null
        ];
        switch ($paginationFormate) {
            case PaginationFormateEnum::normal:
                $finalResult = [
                    'data' => $query->paginate(perPage: $perPage, page: $page),
                    'pagination' => null
                ];
                break;
            case PaginationFormateEnum::separated:
                $result = $query->paginate(perPage: $perPage, page: $page);
                $finalResult = self::separatedPaginate($result);
                break;
            case PaginationFormateEnum::none:
            default:
                $finalResult = [
                    'data' => $query->get(),
                    'pagination' => null
                ];
                break;
        }
        return $finalResult;
    }

    public static function separatedPaginate($paginate)
    {
        $data = $paginate->getCollection();
        $result = collect($paginate)->toArray();
        unset($result['data']);
        return [
            'data' => $data,
            'pagination' => $result
        ];
    }

    public function count(): int
    {
        return $this->model->query()->select([$this->model->getKeyName()])->count();
    }


    public static function getFiltersValuesFromRequest($request)
    {
        $filters = collect([]);
        $decodedFilters = json_decode(base64_decode($request->input('filters'))) ?? [];

        foreach (collect($decodedFilters) as $value) {
            $arrayValue = (array)$value;
            if (isset($value->id, $value->value, $value->filterFns)) {
                $filters->push(new ColumnFilterData(
                    id: $value->id,
                    value: $value->value,
                    filterFns: FilterFnsEnum::from($value->filterFns),
                ));
            }
        }
        return $filters;
    }

    public static function getSortingValuesFromRequest($request)
    {
        $sorting = collect([]);
        $decodedSorting = json_decode(base64_decode($request->input('sorting'))) ?? [];
        foreach (collect($decodedSorting) as $value) {
            if (isset($value->id, $value->desc)) {
                $sorting->push(new ColumnSortData(
                    id: $value->id,
                    desc: $value->desc,
                ));
            }
        }
        return $sorting;
    }

    /**
     * Decodes and retrieves the advanced filter object from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return object|null
     */
    public static function getAdvanceFilterFromRequest($request): ?object
    {
        $advanceFilterInput = $request->input('advanceFilter');

        if (!$advanceFilterInput) {
            return null;
        }

        return json_decode(base64_decode($advanceFilterInput));
    }


    /**
     * Builds the SELECT clause using aliases from the JoinManager.
     */
    private function buildSelectClause(Builder $query, ?string $fields): void
    {
        $model = $query->getModel();
        $mainTableAlias = $this->joinManager->getMainTableAlias();
        $primaryKey = $model->definePrimaryKeyName();
        $selectColumns = ["{$mainTableAlias}.{$primaryKey}"];

        if (empty($fields)) {
            $query->select("{$mainTableAlias}.*");
            return;
        }

        $requestedFields = array_filter(explode(',', $fields));
        $columnsMap = $model->defineFieldSelectionMap();
        $relationships = $model->defineRelationships();

        foreach ($requestedFields as $field) {
            $trimmedField = trim($field);
            if (!isset($columnsMap[$trimmedField])) continue;

            $dbColumnIdentifier = $columnsMap[$trimmedField];

            // Check if it's a related column (e.g., 'translations.title')
            if (str_contains($dbColumnIdentifier, '.')) {
                [$relationName, $columnName] = explode('.', $dbColumnIdentifier, 2);

                // Ensure the relation is defined in the model's whitelist
                if (isset($relationships[$relationName])) {
                    $relationMethod = $relationships[$relationName];
                    $alias = $this->joinManager->ensureJoin($relationMethod);
                    // Add the aliased column, with a final alias to prevent name collision in results
                    $selectColumns[] = "{$alias}.{$columnName} as {$relationName}_{$columnName}";
                }
            } else {
                // It's a column on the main table
                $selectColumns[] = "{$mainTableAlias}.{$dbColumnIdentifier}";
            }
        }
        $query->select(array_unique($selectColumns));
    }

    public static function dynamicSearchFromRequest(
        $model,
        $page = null,
        $perPage = null,
        $paginationFormate = null,
        $filters = null,
        $sorting = null,
        $globalFilter = null,
        $advanceFilter = null,
        $globaleFilterExtraOperation = null,
        $extraOperation = null,
        $beforeOperation = null,
        array $filterKeyMap = [],
        array $sortKeyMap = [],
        $fields = null
    ) {
        $request = request();

        $service = new AutoFilterAndSortService($model);

        $dynamicFilterData = $service->initializeDynamicFilterData(
            request: $request,
            page: $page,
            perPage: $perPage,
            paginationFormate: $paginationFormate,
            filters: $filters,
            sorting: $sorting,
            globalFilter: $globalFilter,
            advanceFilter: $advanceFilter,
            globaleFilterExtraOperation: $globaleFilterExtraOperation,
            extraOperation: $extraOperation,
            beforeOperation: $beforeOperation,
            filterKeyMap: $filterKeyMap,
            sortKeyMap: $sortKeyMap,
            fields: $fields
        );

        return $service->dynamicFilter($dynamicFilterData);
    }
}
