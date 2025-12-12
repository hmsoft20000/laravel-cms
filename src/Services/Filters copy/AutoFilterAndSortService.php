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
use Illuminate\Support\Collection;

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
        $fields = null,
        $count_only = null // [NEW]
    ): DynamicFilterData {
        if ($dynamicFilterData) {
            return $dynamicFilterData;
        }

        $request = $request ?? request();

        $finalPage = $page ?? $request->input('page');
        $finalPerPage = $perPage ?? $request->input('perPage', $request->input('per_page', $request->input('limit')));

        // [NEW] Read count_only from request
        $finalCountOnly = $count_only !== null ? (bool) $count_only : (bool) $request->input('count_only', false);

        $finalPaginationFormate = is_null($paginationFormate)
            ? PaginationFormateEnum::from($request->input('paginationFormate', PaginationFormateEnum::separated->value))
            : $paginationFormate;

        // [MODIFIED] count_only also forces pagination to 'none'
        if (is_null($finalPage) || is_null($finalPerPage) || $finalPerPage === 'all' || $request->header('pdt') === '0' || $finalCountOnly) {
            $finalPaginationFormate = PaginationFormateEnum::none;
            // if (!$finalCountOnly) {
            $finalPage = 'all';
            $finalPerPage = 'all';
            // }
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
            fields: $finalFields,
            count_only: $finalCountOnly
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
        $tableName = $this->model->getTable();
        // $mainTableAlias = 't_main'; // The alias for the main table.
        $mainTableAlias = $tableName; // The alias for the main table.
        $query->from($tableName, $mainTableAlias); // Apply the alias immediately.


        $this->joinManager = new JoinManager($query, $mainTableAlias);

        // 1. Build dynamic SELECT clause (now alias-aware).
        // [MODIFIED] Optimization: Don't build SELECT if we only need the count.
        if (!$dynamicFilterData->count_only) {
            $this->buildSelectClause($query, $dynamicFilterData->fields);
        }

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
        // [MODIFIED] Optimization: Don't apply sorting if we only need the count.
        if (!$dynamicFilterData->count_only) {
            self::handelSorting($query, $sortingKeys, $this->joinManager);
        }

        // 8. Group By using the main table alias to ensure distinct results.
        // (This is required for the count to be correct after joins).
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
                    }
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

        // [NEW] Handle count_only flag
        // If true, perform the optimized count and return immediately.
        if ($dynamicFilterData->count_only) {
            $countQuery = clone $query;
            $countQuery->getQuery()->orders = null; // Remove sorting for count
            $countQuery->getQuery()->columns = null; // Remove selects for count
            $countQuery->select(DB::raw('1')); // Select minimal column

            // Use the existing subquery count logic which is correct for GROUP BY
            $totalRecords = DB::connection($this->model->getConnectionName())
                ->table(DB::raw("({$countQuery->toSql()}) as sub"))
                ->mergeBindings($countQuery->getQuery())
                ->count();

            return [
                'data' => $totalRecords,
                'pagination' => null
            ];
        }
        // [END NEW]

        Log::info($query->toRawSql());

        // This code now effectively becomes the 'else' block for when count_only is false.
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
        // --- الخطوة 1: جلب أنواع السمات المطلوبة ---
        $attributeIdsToFetch = [];
        foreach ($sortedColumns as $columnCollection) {
            $columnId = $columnCollection[0]->id;
            if (str_starts_with($columnId, CustomAttributeFilter::ATTRIBUTE_PREFIX)) {
                $attributeIdsToFetch[] = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $columnId);
            }
        }

        /** @var \Illuminate\Support\Collection $attributes */
        // جلب السمات المطلوبة للترتيب في استعلام واحد (لمنع N+1)
        $attributes = !empty($attributeIdsToFetch)
            ? resolve(\HMsoft\Cms\Models\Shared\Attribute::class)->whereIn('id', $attributeIdsToFetch)->get()->keyBy('id')
            : collect();
        // --- [نهاية الخطوة 1] ---


        // (جلب المعلومات الأساسية)
        $model = $query->getModel();
        $avTable = resolve(\HMsoft\Cms\Models\Shared\AttributeValue::class)->getTable();
        $mainTableAlias = $joinManager->getMainTableAlias();
        $mainKey = $model->getKeyName();
        $mainMorphClass = $model->getMorphClass();

        foreach ($sortedColumns as $columnCollection) {
            $sortingValue = $columnCollection[0];
            $columnId = $sortingValue->id;
            $sortDirection = $sortingValue->desc ? 'desc' : 'asc';

            // (سيحتوي هذا المتغير على التعبير الذي سيتم الترتيب بناءً عليه)
            $sortExpression = null;

            if (str_starts_with($columnId, CustomAttributeFilter::ATTRIBUTE_PREFIX)) {
                try {
                    $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $columnId);

                    /** @var \HMsoft\Cms\Models\Shared\Attribute|null $attribute */
                    $attribute = $attributes->get($attributeId);

                    $sortAlias = "sort_av_{$attributeId}";
                    $sortColumnAlias = "sort_col_{$attributeId}";

                    $query->leftJoin(
                        "{$avTable} as {$sortAlias}",
                        function ($join) use ($sortAlias, $mainTableAlias, $mainKey, $mainMorphClass, $attributeId) {
                            $join->on("{$sortAlias}.owner_id", '=', "{$mainTableAlias}.{$mainKey}")
                                ->where("{$sortAlias}.owner_type", $mainMorphClass)
                                ->where("{$sortAlias}.attribute_id", $attributeId);
                        }
                    );

                    // --- الخطوة 2: تطبيق CAST بشكل مشروط ---
                    $valueColumn = "MAX({$sortAlias}.value)"; // الافتراضي (نصي)

                    if ($attribute && in_array($attribute->type, ['number', 'year'])) {
                        // إذا كان رقم، استخدم CAST كـ SIGNED (رقم)
                        $valueColumn = "MAX(CAST({$sortAlias}.value AS SIGNED))";
                    } elseif ($attribute && in_array($attribute->type, ['date', 'datetime'])) {
                        // إذا كان تاريخ، استخدم CAST كـ DATETIME
                        $valueColumn = "MAX(CAST({$sortAlias}.value AS DATETIME))";
                    }
                    // (الأنواع الأخرى ستبقى نصية)

                    $sortExpression = $valueColumn;

                    // إضافة الـ SELECT (ضروري لـ GROUP BY)
                    $query->addSelect(DB::raw("{$sortExpression} as {$sortColumnAlias}"));
                } catch (\Exception $e) {
                    Log::error("Failed to apply custom attribute sort for '{$columnId}': " . $e->getMessage());
                    continue;
                }
            } else {
                // هذا عمود عادي (مثل 'created_at')
                $sortExpression = self::resolveAndJoin($columnId, $joinManager, $model);
            }

            if (empty($sortExpression)) {
                continue;
            }

            // --- الخطوة 3: تطبيق الترتيب (مع NULLs في النهاية) ---
            // (يستخدم التعبير مباشرة لحل خطأ MySQL 1247)
            $query->orderByRaw("{$sortExpression} IS NULL ASC, {$sortExpression} {$sortDirection}");
        }
    }

    // public static function handelSorting(Builder $query, $sortedColumns, JoinManager $joinManager): void
    // {
    //     // جلب المعلومات الأساسية المطلوبة
    //     $model = $query->getModel();
    //     $avTable = resolve(\HMsoft\Cms\Models\Shared\AttributeValue::class)->getTable();
    //     $mainTableAlias = $joinManager->getMainTableAlias();
    //     $mainKey = $model->getKeyName();
    //     $mainMorphClass = $model->getMorphClass();

    //     foreach ($sortedColumns as $columnCollection) {
    //         $sortingValue = $columnCollection[0];
    //         $columnId = $sortingValue->id; // e.g., 'attribute_2' or 'created_at'
    //         $sortDirection = $sortingValue->desc ? 'desc' : 'asc';

    //         $sortExpression = null;

    //         // --- [المنطق الجديد] ---
    //         // التحقق إذا كان الترتيب لسمة مخصصة
    //         if (str_starts_with($columnId, CustomAttributeFilter::ATTRIBUTE_PREFIX)) {
    //             try {
    //                 $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $columnId);

    //                 // 1. إنشاء alias فريد لهذا الـ JOIN
    //                 $sortAlias = "sort_av_{$attributeId}";
    //                 // 2. إنشاء alias لعمود الـ SELECT
    //                 $sortColumnAlias = "sort_col_{$attributeId}";

    //                 // 3. إضافة LEFT JOIN لجدول attribute_values
    //                 $query->leftJoin(
    //                     "{$avTable} as {$sortAlias}",
    //                     function ($join) use ($sortAlias, $mainTableAlias, $mainKey, $mainMorphClass, $attributeId) {
    //                         $join->on("{$sortAlias}.owner_id", '=', "{$mainTableAlias}.{$mainKey}")
    //                             ->where("{$sortAlias}.owner_type", $mainMorphClass)
    //                             ->where("{$sortAlias}.attribute_id", $attributeId);
    //                     }
    //                 );

    //                 $sortExpression = "MAX({$sortAlias}.value)";
    //                 $query->addSelect(DB::raw("{$sortExpression} as {$sortColumnAlias}"));
    //             } catch (\Exception $e) {
    //                 Log::error("Failed to apply custom attribute sort for '{$columnId}': " . $e->getMessage());
    //                 continue; // تجاهل هذا الترتيب إذا فشل
    //             }
    //         } else {
    //             // هذا عمود عادي (مثل created_at)، استخدم المنطق القديم
    //             $sortExpression = self::resolveAndJoin($columnId, $joinManager, $model);
    //         }
    //         // --- [نهاية المنطق الجديد] ---

    //         if (empty($sortExpression)) {
    //             continue;
    //         }

    //         // لأننا نستخدم alias مُجمع (MAX)، يجب أن نطبق 'orderBy' مباشرة
    //         // بدلاً من تمريره إلى 'ColumnSortData'
    //         // $query->orderBy($qualifiedSortColumn, $sortDirection);
    //         // $query->orderByRaw("{$qualifiedSortColumn} IS NULL ASC, {$qualifiedSortColumn} {$sortDirection}");
    //         $query->orderByRaw("{$sortExpression} IS NULL ASC, {$sortExpression} {$sortDirection}");
    //     }
    // }

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


    public static function getFiltersValuesFromRequest($request): Collection
    {
        $filters = collect([]);
        $encodedFilters = $request->input('filters');

        if (empty($encodedFilters)) {
            return $filters;
        }

        $decodedFilters = self::smartDecode($encodedFilters, 'Filters');

        if ($decodedFilters === null) {
            return $filters;
        }

        foreach ($decodedFilters as $filter) {
            if (is_array($filter) && isset($filter['id'], $filter['value'], $filter['filterFns'])) {
                $filterFnEnum = FilterFnsEnum::tryFrom($filter['filterFns']);
                if ($filterFnEnum) {
                    $filters->push(new ColumnFilterData(
                        id: $filter['id'],
                        value: $filter['value'],
                        filterFns: $filterFnEnum,
                    ));
                }
            }
        }

        return $filters;
    }

    /**
     * استخراج الترتيب من الطلب
     */
    public static function getSortingValuesFromRequest($request): Collection
    {
        $sorting = collect([]);
        $encodedSorting = $request->input('sorting');

        if (empty($encodedSorting)) {
            return $sorting;
        }

        $decodedSorting = self::smartDecode($encodedSorting, 'Sorting');

        if ($decodedSorting === null) {
            return $sorting;
        }

        foreach ($decodedSorting as $sort) {
            if (is_array($sort) && isset($sort['id'], $sort['desc'])) {
                $sorting->push(new ColumnSortData(
                    id: $sort['id'],
                    desc: (bool)$sort['desc']
                ));
            }
        }

        return $sorting;
    }

    /**
     * استخراج الفلاتر المتقدمة من الطلب
     */
    public static function getAdvanceFilterFromRequest($request): ?array
    {
        $encodedAdvanceFilter = $request->input('advanceFilter');

        if (empty($encodedAdvanceFilter)) {
            return null;
        }

        $decodedAdvanceFilter = self::smartDecode($encodedAdvanceFilter, 'AdvanceFilter');

        if ($decodedAdvanceFilter === null) {
            return null;
        }

        return $decodedAdvanceFilter;
    }

    /**
     * -------------------------------------------------------------------
     * ℹ️ الدالة المساعدة (smartDecode) - (النسخة الصحيحة)
     * -------------------------------------------------------------------
     * معالج ذكي متعدد الطبقات لفك تشفير البارامترات
     */
    private static function smartDecode(string $encodedData, string $paramName = 'data'): ?array
    {

        // المرحلة 0: إصلاح أحرف URL-Safe (آمن لجميع التنسيقات)
        $b64Standard = str_replace(['-', '_'], ['+', '/'], $encodedData);

        // المرحلة 1: فك Base64 (الطبقة 1)
        $step1 = base64_decode($b64Standard, true);
        if ($step1 === false) {
            return null;
        }

        // المرحلة 2: اختبار التنسيق القديم (Legacy)
        $jsonAttempt = json_decode($step1, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt)) {
            return $jsonAttempt;
        }

        // المرحلة 3: اختبار التنسيق المضغوط (Compressed)

        // 3أ: محاولة Deflate
        $inflateAttempt = @gzinflate($step1);
        if ($inflateAttempt !== false) {
            $jsonAttempt = json_decode($inflateAttempt, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt)) {
                return $jsonAttempt;
            }
        }

        // 3ب: محاولة Gzip
        $gzipAttempt = @gzdecode($step1);
        if ($gzipAttempt !== false) {
            $jsonAttempt = json_decode($gzipAttempt, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt)) {
                return $jsonAttempt;
            }
        }

        // المرحلة 4: اختبار التنسيق الخاطئ (Buggy / Double-Encoded)
        // إذا فشل كل ما سبق، نفترض أن $step1 هو نفسه نص Base64

        $step2 = @base64_decode($step1, true);
        if ($step2 !== false) {
            // 4أ: محاولة Deflate
            $inflateAttempt2 = @gzinflate($step2);
            if ($inflateAttempt2 !== false) {
                $jsonAttempt2 = json_decode($inflateAttempt2, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt2)) {
                    return $jsonAttempt2;
                }
            }
            // 4ب: محاولة Gzip
            $gzipAttempt2 = @gzdecode($step2);
            if ($gzipAttempt2 !== false) {
                $jsonAttempt2 = json_decode($gzipAttempt2, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt2)) {
                    return $jsonAttempt2;
                }
            }
        }
        return null;
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
        $fields = null,
        $count_only = null // [NEW]
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
            fields: $fields,
            count_only: $count_only // [NEW]
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
