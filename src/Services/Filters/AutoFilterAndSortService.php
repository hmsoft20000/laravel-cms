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
use HMsoft\Cms\Models\Shared\Attribute;
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
            ->filter(fn(ColumnFilterData $filter) => in_array($filter->id, $allowedFilters))
            ->values();


        $dynamicFilterData->sorting = collect($dynamicFilterData->sorting)
            ->filter(fn(ColumnSortData $sort) => in_array($sort->id, $allowedSorts))
            ->values();

        $pFilterKeys = collect($dynamicFilterData->filters)->groupBy('id');
        $sortingKeys = collect($dynamicFilterData->sorting)->groupBy('id');

        // 3. Apply "before" hook for any initial query modifications.
        if (isset($beforeOperation)) {
            $beforeOperation(
                $query,
                ['filterKeys' => $pFilterKeys, 'sortingKeys' => $sortingKeys, 'mainTableAlias' => $mainTableAlias]
            );
        }

        // 4. Apply Filters (Advanced vs. Simple), now alias-aware.
        if (!empty($dynamicFilterData->advanceFilter)) {

            // --- Pre-fetch custom attributes for the advanced filter ---
            $attributeIds = self::extractAttributeIdsFromGroup($dynamicFilterData->advanceFilter);
            $attributes = !empty($attributeIds)
                ? Attribute::whereIn('id', $attributeIds)->get()->keyBy('id')
                : collect();

            $query->where(function (Builder $builder) use ($dynamicFilterData, $allowedFilters, $attributes) {
                // Pass the pre-fetched attributes to the handler
                self::applyAdvancedFilterGroup($builder, $dynamicFilterData->advanceFilter, $allowedFilters, $this->joinManager, $attributes);
            });
        } else {

            // 1. Separate custom attribute filters to prepare them.
            $customAttributeFilters = collect($dynamicFilterData->filters)->filter(
                fn($filter) => CustomAttributeFilter::isCustomAttribute($filter)
            );

            // 2. Extract their IDs.
            $attributeIds = $customAttributeFilters->map(
                fn($filter) => (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id)
            )->unique()->toArray();

            // 3. Fetch all required Attribute models in a SINGLE query to prevent N+1 problem.
            $attributes = !empty($attributeIds)
                ? Attribute::whereIn('id', $attributeIds)->get()->keyBy('id')
                : collect();

            foreach ($dynamicFilterData->filters as $filter) {
                if (CustomAttributeFilter::isCustomAttribute($filter)) {
                    // Custom attribute logic needs to be aware of the main table alias.
                    $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id);
                    // Get the pre-fetched attribute object from the collection.
                    $attribute = $attributes->get($attributeId);
                    // Only apply the filter if the attribute exists.
                    if ($attribute) {
                        // Call the NEW method signature, passing the pre-fetched $attribute.
                        CustomAttributeFilter::apply($query, $attribute, $filter, $this->model);
                    }
                } else {
                    if (isset($pFilterKeys[$filter->id])) {
                        self::handelFilterOne($query, collect($pFilterKeys[$filter->id])->toArray(), $filter->id, $this->joinManager);
                    }
                }
            }
        }

        // 5. Apply Global Filter (now alias-aware).
        if (isset($dynamicFilterData->globalFilter) && !empty($dynamicFilterData->globalFilter)) {
            $this->applyGlobalFilter($query, $dynamicFilterData->globalFilter, $globaleFilterExtraOperation, $pFilterKeys, $sortingKeys, $mainTableAlias);
        }

        // 6. Apply the "extra" hook for any final query modifications.
        if (isset($extraOperation)) {
            $extraOperation(
                $query,
                [
                    'filterKeys' => $pFilterKeys,
                    'sortingKeys' => $sortingKeys,
                    'globalFilter' => $dynamicFilterData->globalFilter,
                    'mainTableAlias'    => $mainTableAlias
                ]
            );
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

            // 1. Search base attributes (no change)
            foreach ($this->model->defineGlobalSearchBaseAttributes() as $column) {
                $builder->orWhere("{$mainTableAlias}.{$column}", 'LIKE', "%{$globalFilterValue}%");
            }

            // 2. [NEW] Search related attributes in a generic way
            if (method_exists($this->model, 'defineGlobalSearchRelatedAttributes')) {
                $relatedSearchAttrs = $this->model->defineGlobalSearchRelatedAttributes();

                foreach ($relatedSearchAttrs as $relationPath => $columns) {
                    try {
                        $relationAlias = $this->joinManager->ensureJoin($relationPath);
                        foreach ($columns as $column) {
                            $builder->orWhere("{$relationAlias}.{$column}", 'LIKE', "%{$globalFilterValue}%");
                        }
                    } catch (\Exception $e) {
                        // Optional: Log if a defined search relationship is invalid
                        // Log::warning("Global search skipped for invalid relation path '{$relationPath}' in model " . get_class($this->model));
                    }
                }
            }
        });
    }
    // private function applyGlobalFilter(Builder $query, string $globalFilterValue): void
    // {
    //     $mainTableAlias = $this->joinManager->getMainTableAlias();

    //     $query->where(function (Builder $builder) use ($globalFilterValue, $mainTableAlias) {
    //         // Search base attributes
    //         foreach ($this->model->defineGlobalSearchBaseAttributes() as $column) {
    //             $builder->orWhere("{$mainTableAlias}.{$column}", 'LIKE', "%{$globalFilterValue}%");
    //         }

    //         // Search translation attributes
    //         $relationships = $this->model->defineRelationships();
    //         if ($this->model->defineTranslationTableName() && isset($relationships['translations'])) {
    //             $translationAlias = $this->joinManager->ensureJoin($relationships['translations']);
    //             foreach ($this->model->defineGlobalSearchTranslationAttributes() as $column) {
    //                 $builder->orWhere("{$translationAlias}.{$column}", 'LIKE', "%{$globalFilterValue}%");
    //             }
    //         }
    //     });
    // }

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
     * Recursively applies a group of advanced filter rules, now aware of custom attributes.
     */
    private static function applyAdvancedFilterGroup(Builder $query, object $filterGroup, array $allowedFilters, JoinManager $joinManager, \Illuminate\Support\Collection $attributes): void
    {
        $condition = strtoupper($filterGroup->condition ?? 'AND') === 'OR' ? 'orWhere' : 'where';

        foreach ($filterGroup->rules ?? [] as $rule) {
            if (isset($rule->condition)) {
                // If the rule is another group, recurse into it.
                $query->{$condition}(function (Builder $subQuery) use ($rule, $allowedFilters, $joinManager, $attributes) {
                    self::applyAdvancedFilterGroup($subQuery, $rule, $allowedFilters, $joinManager, $attributes);
                });
            } elseif (isset($rule->id)) {
                // It's a simple rule, check if it's allowed.
                if (!in_array($rule->id, $allowedFilters)) {
                    continue; // Security check
                }

                // Convert the stdClass rule to a ColumnFilterData DTO
                $filterData = new ColumnFilterData(
                    id: $rule->id,
                    value: $rule->value,
                    filterFns: FilterFnsEnum::from($rule->filterFns),
                );

                // --- NEW LOGIC: Route the filter to the correct handler ---
                if (CustomAttributeFilter::isCustomAttribute($filterData)) {
                    $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filterData->id);
                    $attribute = $attributes->get($attributeId);

                    if ($attribute) {
                        // We need a specific context for this single filter
                        $query->{$condition}(function (Builder $subQuery) use ($attribute, $filterData, $query) {
                            CustomAttributeFilter::apply($subQuery, $attribute, $filterData, $query->getModel());
                        });
                    }
                } else {
                    // It's a regular filter, apply it with the correct condition (AND/OR).
                    $whereClause = $condition === 'orWhere' ? 'OR' : 'AND';
                    self::applySimpleFilterRule($query, $rule, $whereClause, $joinManager);
                }
            }
        }
    }


    /**
     * Helper method to apply a single filter rule from an advanced filter group, now using the JoinManager.
     */
    private static function applySimpleFilterRule(Builder $query, object $rule, string $conditionType, JoinManager $joinManager): void
    {
        $qualifiedColumnId = self::resolveAndJoin($rule->id, $joinManager, $query->getModel());
        if (empty($qualifiedColumnId)) {
            return; // If the column could not be resolved, ignore the filter.
        }
        $filterData = new ColumnFilterData(
            id: $qualifiedColumnId,
            value: $rule->value,
            filterFns: FilterFnsEnum::from($rule->filterFns),
        );
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
        $qualifiedColumnId = self::resolveAndJoin($columnId, $joinManager, $query->getModel());
        if (empty($qualifiedColumnId)) {
            return; // Ignore if column/relation is not valid.
        }

        $filterData = $filterObjects[0];
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
        foreach ($sortedColumns as $columnCollection) {
            $sortingValue = $columnCollection[0];
            $columnId = $sortingValue->id;

            $qualifiedColumnId = self::resolveAndJoin($columnId, $joinManager, $query->getModel());

            if (empty($qualifiedColumnId)) {
                continue; // Ignore if column/relation is not valid.
            }

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
                    'data' => $query->paginate(perPage: (int)$perPage, page: (int) $page),
                    'pagination' => null
                ];
                break;
            case PaginationFormateEnum::separated:
                $result = $query->paginate(perPage: (int) $perPage, page: (int) $page);
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


    /**
     * [NEW CENTRAL HELPER]
     * Resolves a public-facing column ID into a qualified, aliased database column name.
     * This static helper can be used by other static methods.
     *
     * @param string $columnId The public column identifier (e.g., 'categories.name').
     * @param JoinManager $joinManager The active JoinManager instance.
     * @param Model|AutoFilterable $model The model instance to resolve relationships from.
     * @return string|null The aliased column name or null if invalid.
     */
    private static function resolveAndJoin(string $columnId, JoinManager $joinManager, Model $model): ?string
    {
        if (!str_contains($columnId, '.')) {
            // This is a column on the main table.
            return $joinManager->getMainTableAlias() . '.' . $columnId;
        }

        $parts = explode('.', $columnId);
        $columnName = array_pop($parts);
        $relationPath = implode('.', $parts);

        // Get the defined relationships from the model to validate the path.
        // For a deeper validation, one would check each part of the path.
        $definedRelations = $model->defineRelationships();
        $rootRelation = $parts[0];

        if (!isset($definedRelations[$rootRelation])) {
            // The root of the path is not a defined joinable relationship.
            return null;
        }

        try {
            // Ask the JoinManager to handle the entire path.
            $finalAlias = $joinManager->ensureJoin($relationPath);
            return "{$finalAlias}.{$columnName}";
        } catch (\Exception $e) {
            // Log::warning("Could not resolve column '{$columnId}': {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Recursively extracts all custom attribute IDs from an advanced filter group.
     *
     * @param object $filterGroup
     * @return array
     */
    private static function extractAttributeIdsFromGroup(object $filterGroup): array
    {
        $attributeIds = [];

        foreach ($filterGroup->rules ?? [] as $rule) {
            if (isset($rule->condition)) {
                // It's a nested group, recurse into it.
                $attributeIds = array_merge($attributeIds, self::extractAttributeIdsFromGroup($rule));
            } elseif (isset($rule->id) && str_starts_with($rule->id, CustomAttributeFilter::ATTRIBUTE_PREFIX)) {
                // It's a custom attribute rule, extract the ID.
                $attributeIds[] = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $rule->id);
            }
        }

        return array_unique($attributeIds);
    }
}
