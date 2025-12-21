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
use Illuminate\Support\Str;

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
        $columns = null, // [CHANGED] Renamed from fields to columns
        $count_only = null
    ): DynamicFilterData {
        if ($dynamicFilterData) {
            return $dynamicFilterData;
        }

        $request = $request ?? request();

        $finalPage = $page ?? $request->input('page');
        $finalPerPage = $perPage ?? $request->input('perPage', $request->input('per_page', $request->input('limit')));
        $finalCountOnly = $count_only !== null ? (bool) $count_only : (bool) $request->input('count_only', false);

        $finalPaginationFormate = is_null($paginationFormate)
            ? PaginationFormateEnum::from($request->input('paginationFormate', PaginationFormateEnum::separated->value))
            : $paginationFormate;

        if (is_null($finalPage) || is_null($finalPerPage) || $finalPerPage === 'all' || $request->header('pdt') === '0' || $finalCountOnly) {
            $finalPaginationFormate = PaginationFormateEnum::none;
            $finalPage = 'all';
            $finalPerPage = 'all';
        }

        $finalFilters = $filters ?? self::getFiltersValuesFromRequest($request);
        $finalSorting = $sorting ?? self::getSortingValuesFromRequest($request);
        $finalAdvanceFilter = $advanceFilter ?? self::getAdvanceFilterFromRequest($request);
        $finalGlobalFilter = $globalFilter ?? $request->input('globalFilter');

        // [CHANGED] Read columns from request instead of fields
        $finalColumns = $columns ?? $request->input('columns');

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
            columns: $finalColumns, // [CHANGED] Pass columns
            count_only: $finalCountOnly
        );
    }

    // public function buildQuery(?DynamicFilterData $dynamicFilterData = null, bool $applySorting = true): Builder
    // {
    //     if (!($this->model instanceof AutoFilterable)) {
    //         throw new \Exception('Model ' . get_class($this->model) . ' must implement the AutoFilterable interface.');
    //     }

    //     if (!$dynamicFilterData) {
    //         $dynamicFilterData = $this->initializeDynamicFilterData();
    //     }

    //     $query = $this->model->query();
    //     $tableName = $this->model->getTable();
    //     $mainTableAlias = $tableName;
    //     $query->from($tableName, $mainTableAlias);

    //     $this->joinManager = new JoinManager($query, $mainTableAlias);

    //     // 1. Build dynamic SELECT clause.
    //     if (!$dynamicFilterData->count_only) {
    //         $this->buildSelectClause($query, $dynamicFilterData->columns);
    //     }

    //     $extraOperation = $dynamicFilterData->extraOperation;
    //     $globaleFilterExtraOperation = $dynamicFilterData->globaleFilterExtraOperation;
    //     $beforeOperation = $dynamicFilterData->beforeOperation;

    //     $allowedFilters = $this->model->defineFilterableAttributes();
    //     $allowedSorts = $this->model->defineSortableAttributes();

    //     $dynamicFilterData->filters = collect($dynamicFilterData->filters)
    //         ->filter(fn(ColumnFilterData $filter) => in_array($filter->id, $allowedFilters))
    //         ->values();

    //     $dynamicFilterData->sorting = collect($dynamicFilterData->sorting)
    //         ->filter(fn(ColumnSortData $sort) => in_array($sort->id, $allowedSorts))
    //         ->values();

    //     // [REMOVED] Priority Sorting Logic (definePrioritizedAttributes) was here.
    //     // Filters are now processed in the order they were received.

    //     $pFilterKeys = collect($dynamicFilterData->filters)->groupBy('id');
    //     $sortingKeys = collect($dynamicFilterData->sorting)->groupBy('id');

    //     if (isset($beforeOperation)) {
    //         $beforeOperation(
    //             $query,
    //             ['filterKeys' => $pFilterKeys, 'sortingKeys' => $sortingKeys, 'mainTableAlias' => $mainTableAlias]
    //         );
    //     }

    //     // if (!empty($dynamicFilterData->advanceFilter)) {
    //     //     $attributeIds = self::extractAttributeIdsFromGroup($dynamicFilterData->advanceFilter);
    //     //     $attributes = !empty($attributeIds)
    //     //         ? Attribute::whereIn('id', $attributeIds)->get()->keyBy('id')
    //     //         : collect();

    //     //     $query->where(function (Builder $builder) use ($dynamicFilterData, $allowedFilters, $attributes) {
    //     //         self::applyAdvancedFilterGroup($builder, $dynamicFilterData->advanceFilter, $allowedFilters, $attributes);
    //     //     });
    //     // } else {
    //     //     $customAttributeFilters = collect($dynamicFilterData->filters)->filter(
    //     //         fn($filter) => CustomAttributeFilter::isCustomAttribute($filter)
    //     //     );

    //     //     $attributeIds = $customAttributeFilters->map(
    //     //         fn($filter) => (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id)
    //     //     )->unique()->toArray();

    //     //     $attributes = !empty($attributeIds)
    //     //         ? Attribute::whereIn('id', $attributeIds)->get()->keyBy('id')
    //     //         : collect();


    //     //     foreach ($dynamicFilterData->filters as $filter) {
    //     //         if (CustomAttributeFilter::isCustomAttribute($filter)) {
    //     //             $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id);
    //     //             $attribute = $attributes->get($attributeId);
    //     //             if ($attribute) {
    //     //                 CustomAttributeFilter::apply($query, $attribute, $filter, $this->model);
    //     //             }
    //     //         } else {
    //     //             if (isset($pFilterKeys[$filter->id])) {
    //     //                 self::handelFilterOne($query, collect($pFilterKeys[$filter->id])->toArray(), $filter->id);
    //     //             }
    //     //         }
    //     //     }
    //     // }

    //     if (!empty($dynamicFilterData->advanceFilter)) {
    //         $attributeIds = self::extractAttributeIdsFromGroup($dynamicFilterData->advanceFilter);
    //         $attributes = !empty($attributeIds)
    //             ? Attribute::whereIn('id', $attributeIds)->get()->keyBy('id')
    //             : collect();

    //         // نستخدم where هنا لضمان دمجها مع الشروط الأخرى بـ AND
    //         $query->where(function (Builder $builder) use ($dynamicFilterData, $allowedFilters, $attributes) {
    //             self::applyAdvancedFilterGroup($builder, $dynamicFilterData->advanceFilter, $allowedFilters, $attributes);
    //         });
    //     }

    //     if ($dynamicFilterData->filters->isNotEmpty()) {
    //         $customAttributeFilters = collect($dynamicFilterData->filters)->filter(
    //             fn($filter) => CustomAttributeFilter::isCustomAttribute($filter)
    //         );

    //         $attributeIds = $customAttributeFilters->map(
    //             fn($filter) => (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id)
    //         )->unique()->toArray();

    //         $attributes = !empty($attributeIds)
    //             ? Attribute::whereIn('id', $attributeIds)->get()->keyBy('id')
    //             : collect();

    //         // تطبيق الفلاتر العادية حلقة تلو الأخرى
    //         // بما أنها تطبق مباشرة على الـ query، فهي تعمل كـ AND بشكل افتراضي مع ما سبق
    //         foreach ($dynamicFilterData->filters as $filter) {
    //             if (CustomAttributeFilter::isCustomAttribute($filter)) {
    //                 $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id);
    //                 $attribute = $attributes->get($attributeId);
    //                 if ($attribute) {
    //                     CustomAttributeFilter::apply($query, $attribute, $filter, $this->model);
    //                 }
    //             } else {
    //                 if (isset($pFilterKeys[$filter->id])) {
    //                     // تمرير المصفوفة كاملة للدالة لضمان التوافق
    //                     self::handelFilterOne($query, collect($pFilterKeys[$filter->id])->toArray(), $filter->id);
    //                 }
    //             }
    //         }
    //     }


    //     if (isset($dynamicFilterData->globalFilter) && !empty($dynamicFilterData->globalFilter)) {
    //         $this->applyGlobalFilter($query, $dynamicFilterData->globalFilter);
    //     }

    //     if (isset($extraOperation)) {
    //         $extraOperation(
    //             $query,
    //             [
    //                 'filterKeys' => $pFilterKeys,
    //                 'sortingKeys' => $sortingKeys,
    //                 'globalFilter' => $dynamicFilterData->globalFilter,
    //                 'mainTableAlias'    => $mainTableAlias
    //             ]
    //         );
    //     }

    //     if ($applySorting && !$dynamicFilterData->count_only) {
    //         self::handelSorting($query, $sortingKeys, $this->joinManager);
    //     }

    //     return $query;
    // }
    public function buildQuery(?DynamicFilterData $dynamicFilterData = null, bool $applySorting = true): Builder
    {
        if (!($this->model instanceof AutoFilterable)) {
            throw new \Exception('Model ' . get_class($this->model) . ' must implement the AutoFilterable interface.');
        }

        if (!$dynamicFilterData) {
            $dynamicFilterData = $this->initializeDynamicFilterData();
        }

        $query = $this->model->query();
        $tableName = $this->model->getTable();
        $mainTableAlias = $tableName;
        $query->from($tableName, $mainTableAlias);

        $this->joinManager = new JoinManager($query, $mainTableAlias);

        // 1. Build dynamic SELECT clause.
        if (!$dynamicFilterData->count_only) {
            $this->buildSelectClause($query, $dynamicFilterData->columns);
        }

        $extraOperation = $dynamicFilterData->extraOperation;
        $globaleFilterExtraOperation = $dynamicFilterData->globaleFilterExtraOperation;
        $beforeOperation = $dynamicFilterData->beforeOperation;

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

        if (isset($beforeOperation)) {
            $beforeOperation(
                $query,
                ['filterKeys' => $pFilterKeys, 'sortingKeys' => $sortingKeys, 'mainTableAlias' => $mainTableAlias]
            );
        }

        // ---------------------------------------------------------
        // التعديل يبدأ هنا: تطبيق Advanced Filters أولاً
        // ---------------------------------------------------------
        if (!empty($dynamicFilterData->advanceFilter)) {
            $attributeIds = self::extractAttributeIdsFromGroup($dynamicFilterData->advanceFilter);
            $attributes = !empty($attributeIds)
                ? Attribute::whereIn('id', $attributeIds)->get()->keyBy('id')
                : collect();

            // نستخدم where هنا لضمان دمجها مع الشروط الأخرى بـ AND
            $query->where(function (Builder $builder) use ($dynamicFilterData, $allowedFilters, $attributes) {
                self::applyAdvancedFilterGroup($builder, $dynamicFilterData->advanceFilter, $allowedFilters, $attributes);
            });
        }

        // ---------------------------------------------------------
        // تطبيق الفلاتر العادية (Standard Filters) دائماً (تم إزالة else)
        // ---------------------------------------------------------
        if ($dynamicFilterData->filters->isNotEmpty()) {
            $customAttributeFilters = collect($dynamicFilterData->filters)->filter(
                fn($filter) => CustomAttributeFilter::isCustomAttribute($filter)
            );

            $attributeIds = $customAttributeFilters->map(
                fn($filter) => (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id)
            )->unique()->toArray();

            $attributes = !empty($attributeIds)
                ? Attribute::whereIn('id', $attributeIds)->get()->keyBy('id')
                : collect();

            // تطبيق الفلاتر العادية حلقة تلو الأخرى
            // بما أنها تطبق مباشرة على الـ query، فهي تعمل كـ AND بشكل افتراضي مع ما سبق
            foreach ($dynamicFilterData->filters as $filter) {
                if (CustomAttributeFilter::isCustomAttribute($filter)) {
                    $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filter->id);
                    $attribute = $attributes->get($attributeId);
                    if ($attribute) {
                        CustomAttributeFilter::apply($query, $attribute, $filter, $this->model);
                    }
                } else {
                    if (isset($pFilterKeys[$filter->id])) {
                        // تمرير المصفوفة كاملة للدالة لضمان التوافق
                        self::handelFilterOne($query, collect($pFilterKeys[$filter->id])->toArray(), $filter->id);
                    }
                }
            }
        }
        // ---------------------------------------------------------
        // نهاية التعديل
        // ---------------------------------------------------------

        if (isset($dynamicFilterData->globalFilter) && !empty($dynamicFilterData->globalFilter)) {
            $this->applyGlobalFilter($query, $dynamicFilterData->globalFilter);
        }

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

        if ($applySorting && !$dynamicFilterData->count_only) {
            self::handelSorting($query, $sortingKeys, $this->joinManager);
        }

        return $query;
    }


    /**
     * Applies the global filter using separate MATCH clauses for each column.
     * Allows using separate Full-Text indexes instead of a single composite index.
     */
    // private function applyGlobalFilter(Builder $query, string $globalFilterValue): void
    // {
    //     $mainTableAlias = $this->joinManager->getMainTableAlias();

    //     // تهيئة النص للبحث (Boolean Mode)
    //     // إضافة '*' للبحث الجزئي
    //     $formattedValue = trim($globalFilterValue) . '*';

    //     $query->where(function (Builder $builder) use ($formattedValue, $mainTableAlias) {

    //         // 1. البحث في الجدول الأساسي (Base Attributes)
    //         $baseAttributes = $this->model->defineGlobalSearchBaseAttributes();

    //         foreach ($baseAttributes as $col) {
    //             // نستخدم MATCH لكل عمود بشكل منفصل
    //             $builder->orWhereRaw(
    //                 "MATCH({$mainTableAlias}.{$col}) AGAINST(? IN BOOLEAN MODE)",
    //                 [$formattedValue]
    //             );
    //         }

    //         // 2. البحث في العلاقات (Related Attributes)
    //         if (method_exists($this->model, 'defineGlobalSearchRelatedAttributes')) {
    //             $relatedSearchAttrs = $this->model->defineGlobalSearchRelatedAttributes();

    //             foreach ($relatedSearchAttrs as $relationPath => $columns) {

    //                 // البحث داخل العلاقة
    //                 $builder->orWhereHas($relationPath, function ($q) use ($columns, $formattedValue) {

    //                     // نفتح قوساً جديداً داخل العلاقة (Grouping)
    //                     $q->where(function ($subQ) use ($columns, $formattedValue) {
    //                         foreach ($columns as $column) {
    //                             // تطبيق MATCH لكل عمود داخل الجدول المرتبط
    //                             $subQ->orWhereRaw(
    //                                 "MATCH({$column}) AGAINST(? IN BOOLEAN MODE)",
    //                                 [$formattedValue]
    //                             );
    //                         }
    //                     });
    //                 });
    //             }
    //         }
    //     });
    // }


    /**
     * Hybrid Search: Uses MATCH for indexed columns and LIKE for others.
     */
    private function applyGlobalFilter(Builder $query, string $globalFilterValue): void
    {
        $mainTableAlias = $this->joinManager->getMainTableAlias();

        // 1. جلب قائمة الأعمدة المفهرسة (Full-Text Whitelist)
        $fullTextColumns = method_exists($this->model, 'defineFullTextSearchableAttributes')
            ? $this->model->defineFullTextSearchableAttributes()
            : [];

        // 2. تجهيز القيم للبحثين
        $matchValue = trim($globalFilterValue) . '*';      // لـ Full-Text
        $likeValue  = '%' . trim($globalFilterValue) . '%'; // لـ LIKE

        $query->where(function (Builder $builder) use ($matchValue, $likeValue, $mainTableAlias, $fullTextColumns) {

            // --- A. البحث في الجدول الأساسي ---
            $baseAttributes = $this->model->defineGlobalSearchBaseAttributes();

            foreach ($baseAttributes as $col) {
                // هل العمود موجود في قائمة الـ Full-Text؟
                if (in_array($col, $fullTextColumns)) {
                    $builder->orWhereRaw(
                        "MATCH({$mainTableAlias}.{$col}) AGAINST(? IN BOOLEAN MODE)",
                        [$matchValue]
                    );
                } else {
                    // إذا لم يكن مفهرساً، نستخدم LIKE
                    $builder->orWhere($mainTableAlias . '.' . $col, 'LIKE', $likeValue);
                }
            }

            // --- B. البحث في العلاقات ---
            if (method_exists($this->model, 'defineGlobalSearchRelatedAttributes')) {
                $relatedSearchAttrs = $this->model->defineGlobalSearchRelatedAttributes();

                foreach ($relatedSearchAttrs as $relationPath => $columns) {
                    $builder->orWhereHas($relationPath, function ($q) use ($columns, $matchValue, $likeValue, $relationPath, $fullTextColumns) {

                        $q->where(function ($subQ) use ($columns, $matchValue, $likeValue, $relationPath, $fullTextColumns) {
                            foreach ($columns as $column) {
                                // مفتاح البحث للعلاقات يكون: اسم_العلاقة.اسم_العمود
                                // مثال: translations.title
                                $configKey = $relationPath . '.' . $column;

                                if (in_array($configKey, $fullTextColumns)) {
                                    $subQ->orWhereRaw(
                                        "MATCH({$column}) AGAINST(? IN BOOLEAN MODE)",
                                        [$matchValue]
                                    );
                                } else {
                                    $subQ->orWhere($column, 'LIKE', $likeValue);
                                }
                            }
                        });
                    });
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
        // 1. Build Query (Without Sorting) for Counting
        $query = $this->buildQuery($dynamicFilterData, false);

        $sortingKeys = collect($dynamicFilterData->sorting)->groupBy('id');

        if ($dynamicFilterData->count_only) {
            $countQuery = clone $query;
            $countQuery->getQuery()->orders = null;
            $countQuery->getQuery()->columns = null;
            $countQuery->select(DB::raw('1'));

            $totalRecords = DB::connection($this->model->getConnectionName())
                ->table(DB::raw("({$countQuery->toSql()}) as sub"))
                ->mergeBindings($countQuery->getQuery())
                ->count();

            return [
                'data' => $totalRecords,
                'pagination' => null
            ];
        }

        // Handle Simple Pagination Types (Skip Count)
        if (in_array($dynamicFilterData->paginationFormate, [
            PaginationFormateEnum::normal_simple,
            PaginationFormateEnum::separated_simple,
        ])) {
            self::handelSorting($query, $sortingKeys, $this->joinManager);

            return $this->handelResultFormate(
                $dynamicFilterData->paginationFormate,
                $dynamicFilterData->page,
                $dynamicFilterData->perPage,
                $query
            );
        }

        // Handle Normal Pagination (With Count)
        $countQuery = clone $query;
        $countQuery->getQuery()->orders = null;
        $countQuery->getQuery()->columns = null;
        $countQuery->select(DB::raw('1'));

        $totalRecords = DB::connection($this->model->getConnectionName())
            ->table(DB::raw("({$countQuery->toSql()}) as sub"))
            ->mergeBindings($countQuery->getQuery())
            ->count();

        // Apply Sorting for final data retrieval
        self::handelSorting($query, $sortingKeys, $this->joinManager);

        $paginationData = $this->handelPageAndPerPage($dynamicFilterData->page, $dynamicFilterData->perPage, $totalRecords);

        return $this->handelResultFormate($dynamicFilterData->paginationFormate, $paginationData['page'], $paginationData['perPage'], $query);
    }

    private static function applyAdvancedFilterGroup(Builder $query, object $filterGroup, array $allowedFilters, \Illuminate\Support\Collection $attributes): void
    {
        $condition = strtoupper($filterGroup->condition ?? 'AND') === 'OR' ? 'orWhere' : 'where';

        foreach ($filterGroup->rules ?? [] as $rule) {
            if (isset($rule->condition)) {
                $query->{$condition}(function (Builder $subQuery) use ($rule, $allowedFilters, $attributes) {
                    self::applyAdvancedFilterGroup($subQuery, $rule, $allowedFilters, $attributes);
                });
            } elseif (isset($rule->id)) {
                if (!in_array($rule->id, $allowedFilters)) {
                    continue;
                }

                $filterData = new ColumnFilterData(
                    id: $rule->id,
                    value: $rule->value,
                    filterFns: FilterFnsEnum::from($rule->filterFns),
                );

                if (CustomAttributeFilter::isCustomAttribute($filterData)) {
                    $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $filterData->id);
                    $attribute = $attributes->get($attributeId);

                    if ($attribute) {
                        $query->{$condition}(function (Builder $subQuery) use ($attribute, $filterData, $query) {
                            CustomAttributeFilter::apply($subQuery, $attribute, $filterData, $query->getModel());
                        });
                    }
                } else {
                    // For regular filters, we need to handle the condition (AND/OR) manually
                    // because handelFilterOne applies logic directly.
                    // We wrap it in a closure to respect the condition.
                    $query->{$condition}(function ($q) use ($rule, $filterData) {
                        // Pass a single-item array to mimic the structure expected by handelFilterOne
                        self::handelFilterOne($q, [$filterData], $rule->id);
                    });
                }
            }
        }
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

    public static function handelFilter(&$query, $filterKeys, $columnPrefix = null)
    {
        $filterKeys->map(function ($filterValueObject, $columnId) use (&$query) {
            self::handelFilterOne($query, $filterValueObject, $columnId);
        });
    }

    /**
     * [UPDATED] Applies a single filter rule using whereHas (EXISTS) logic.
     * Handles Aliases, Deep Relations, and Custom Attributes automatically.
     */
    public static function handelFilterOne(Builder $query, array $filterObjects, string $columnId, ?Model $model = null): void
    {
        // 1. Resolve the Model (if not passed)
        $model = $model ?? $query->getModel();

        // 2. ALIAS RESOLUTION: Check if this ID is an alias in the map
        // Example: 'category_name' -> 'category.translations.name'
        // Example: 'color' -> 'attribute_5'
        if ($model instanceof \HMsoft\Cms\Interfaces\AutoFilterable) {
            $map = $model->defineFieldSelectionMap();
            if (isset($map[$columnId])) {
                $columnId = $map[$columnId];
            }
        }

        // Prepare the filter data
        $filterData = $filterObjects[0];
        $value = is_array($filterData) ? $filterData['value'] : $filterData->value;
        $filterFns = is_array($filterData) ? $filterData['filterFns'] : $filterData->filterFns;
        $filterFnsEnum = is_string($filterFns) ? FilterFnsEnum::from($filterFns) : $filterFns;

        // 3. DECISION: Check types

        // A. Is it a Custom Attribute? (e.g. 'attribute_5')
        // We create a temp ColumnFilterData to check the ID pattern
        $tempFilter = new ColumnFilterData(id: $columnId, value: $value, filterFns: $filterFnsEnum);
        if (CustomAttributeFilter::isCustomAttribute($tempFilter)) {
            $attributeId = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $columnId);
            // Fetch attribute model (Optimized: ideally passed from outside, but fine for single filter)
            $attribute = Attribute::find($attributeId);
            if ($attribute) {
                CustomAttributeFilter::apply($query, $attribute, $tempFilter, $model);
                return;
            }
        }

        // B. Is it a Relation? (e.g. 'category.name')
        if (str_contains($columnId, '.')) {
            // Split into Relation Path and Column Name
            $relationPath = Str::beforeLast($columnId, '.');
            $targetColumn = Str::afterLast($columnId, '.');

            // Apply whereHas (Recursive EXISTS)
            $query->whereHas($relationPath, function (Builder $q) use ($targetColumn, $value, $filterFnsEnum) {
                // Recursive call! This allows handling Custom Attributes INSIDE relations
                // e.g. 'category.color' -> 'category.attribute_5'
                self::handelFilterOne($q, [new ColumnFilterData($targetColumn, $value, $filterFnsEnum)], $targetColumn);
            });
        } else {
            // C. Direct Column Logic
            $simpleFilter = new ColumnFilterData(
                id: $columnId,
                value: $value,
                filterFns: $filterFnsEnum
            );
            // Apply directly on the main query.
            $simpleFilter->buildQueryWhereStatment($query, $simpleFilter, null, true);
        }
    }

    /**
     * Applies sorting using JoinManager (Kept as is, but ensures joins are used only here).
     */
    public static function handelSorting(Builder $query, $sortedColumns, JoinManager $joinManager): void
    {
        $attributeIdsToFetch = [];
        foreach ($sortedColumns as $columnCollection) {
            $columnId = $columnCollection[0]->id;
            if (str_starts_with($columnId, CustomAttributeFilter::ATTRIBUTE_PREFIX)) {
                $attributeIdsToFetch[] = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $columnId);
            }
        }

        $attributes = !empty($attributeIdsToFetch)
            ? resolve(\HMsoft\Cms\Models\Shared\Attribute::class)->whereIn('id', $attributeIdsToFetch)->get()->keyBy('id')
            : collect();

        $model = $query->getModel();
        $avTable = resolve(\HMsoft\Cms\Models\Shared\AttributeValue::class)->getTable();
        $mainTableAlias = $joinManager->getMainTableAlias();
        $mainKey = $model->getKeyName();
        $mainMorphClass = $model->getMorphClass();

        foreach ($sortedColumns as $columnCollection) {
            $sortingValue = $columnCollection[0];
            $columnId = $sortingValue->id;
            $sortDirection = $sortingValue->desc ? 'desc' : 'asc';

            $sortExpression = null;

            // Resolve Alias for Sorting too
            if ($model instanceof \HMsoft\Cms\Interfaces\AutoFilterable) {
                $map = $model->defineFieldSelectionMap();
                if (isset($map[$columnId])) {
                    $columnId = $map[$columnId];
                }
            }

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

                    $valueColumn = "MAX({$sortAlias}.value)";

                    if ($attribute && in_array($attribute->type, ['number', 'year'])) {
                        $valueColumn = "MAX(CAST({$sortAlias}.value AS SIGNED))";
                    } elseif ($attribute && in_array($attribute->type, ['date', 'datetime'])) {
                        $valueColumn = "MAX(CAST({$sortAlias}.value AS DATETIME))";
                    }

                    $sortExpression = $valueColumn;
                    $query->addSelect(DB::raw("{$sortExpression} as {$sortColumnAlias}"));
                } catch (\Exception $e) {
                    Log::error("Failed to apply custom attribute sort for '{$columnId}': " . $e->getMessage());
                    continue;
                }
            } else {
                // Use JoinManager for resolving sorting columns that require joins
                $sortExpression = self::resolveAndJoinForSort($columnId, $joinManager, $model);
            }

            if (empty($sortExpression)) {
                continue;
            }

            $query->orderByRaw("{$sortExpression} IS NULL ASC, {$sortExpression} {$sortDirection}");
        }
    }


    public static function handelResultFormate(
        PaginationFormateEnum $paginationFormate,
        $page,
        $perPage,
        Builder|\Illuminate\Database\Query\Builder &$query
    ): array {
        $finalResult = ['data' => null, 'pagination' => null];

        switch ($paginationFormate) {
            case PaginationFormateEnum::normal:
                $finalResult = [
                    'data' => $query->paginate(perPage: (int)$perPage, page: (int)$page),
                    'pagination' => null
                ];
                break;

            case PaginationFormateEnum::separated:
                $result = $query->paginate(perPage: (int)$perPage, page: (int)$page);
                $finalResult = self::separatedPaginate($result);
                break;

            case PaginationFormateEnum::normal_simple:
                $finalResult = [
                    'data' => $query->simplePaginate(perPage: (int)$perPage, page: (int)$page),
                    'pagination' => null
                ];
                break;
            case PaginationFormateEnum::separated_simple:
                $result = $query->simplePaginate(perPage: (int)$perPage, page: (int)$page);
                $finalResult = self::separatedSimplePaginate($result);
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

    /**
     * Helper to separate data from metadata for Simple Pagination (No Total).
     */
    public static function separatedSimplePaginate($paginate)
    {
        $data = $paginate->items(); // getCollection() sometimes behaves differently on Paginator
        $result = $paginate->toArray();
        unset($result['data']); // Remove data from metadata

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

    public static function getAdvanceFilterFromRequest($request): ?object
    {
        $encodedAdvanceFilter = $request->input('advanceFilter');

        if (empty($encodedAdvanceFilter)) {
            return null;
        }

        // info(['encodedAdvanceFilter' => $encodedAdvanceFilter]); // يمكنك تفعيل الـ Log إذا أردت

        $advanceFilter = self::smartDecode($encodedAdvanceFilter, 'AdvanceFilter');

        if ($advanceFilter === null) {
            return null;
        }

        // التعديل هنا: إزالة true ليتم التحويل إلى object بدلاً من associative array
        return json_decode(json_encode($advanceFilter), false);
    }

    // public static function getAdvanceFilterFromRequest($request): ?object
    // {
    //     $encodedAdvanceFilter = $request->input('advanceFilter');
    //     if (empty($encodedAdvanceFilter)) {
    //         return null;
    //     }
    //     info(['encodedAdvanceFilter' => $encodedAdvanceFilter]);
    //     // convert to object
    //     $advanceFilter = self::smartDecode($encodedAdvanceFilter, 'AdvanceFilter');
    //     if ($advanceFilter === null) {
    //         return null;
    //     }
    //     return json_decode(json_encode($advanceFilter), true);
    // }

    private static function smartDecode(string $encodedData, string $paramName = 'data'): ?array
    {
        $b64Standard = str_replace(['-', '_'], ['+', '/'], $encodedData);
        $step1 = base64_decode($b64Standard, true);
        if ($step1 === false) {
            return null;
        }

        $jsonAttempt = json_decode($step1, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt)) {
            return $jsonAttempt;
        }

        $inflateAttempt = @gzinflate($step1);
        if ($inflateAttempt !== false) {
            $jsonAttempt = json_decode($inflateAttempt, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt)) {
                return $jsonAttempt;
            }
        }

        $gzipAttempt = @gzdecode($step1);
        if ($gzipAttempt !== false) {
            $jsonAttempt = json_decode($gzipAttempt, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt)) {
                return $jsonAttempt;
            }
        }

        $step2 = @base64_decode($step1, true);
        if ($step2 !== false) {
            $inflateAttempt2 = @gzinflate($step2);
            if ($inflateAttempt2 !== false) {
                $jsonAttempt2 = json_decode($inflateAttempt2, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonAttempt2)) {
                    return $jsonAttempt2;
                }
            }
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
     * [CHANGED] Builds the SELECT clause using aliases from the JoinManager.
     * Supports selecting columns from related tables dynamically (e.g. category.name).
     */
    private function buildSelectClause(Builder $query, ?string $columns): void
    {
        $model = $query->getModel();
        $mainTableAlias = $this->joinManager->getMainTableAlias();
        $primaryKey = $model->definePrimaryKeyName();
        $selectColumns = ["{$mainTableAlias}.{$primaryKey}"];

        if (empty($columns)) {
            $query->select("{$mainTableAlias}.*");
            return;
        }

        $requestedColumns = array_filter(explode(',', $columns));
        $columnsMap = $model->defineFieldSelectionMap();

        foreach ($requestedColumns as $column) {
            $trimmedColumn = trim($column);

            // Check Map
            $dbPath = isset($columnsMap[$trimmedColumn]) ? $columnsMap[$trimmedColumn] : $trimmedColumn;

            // Check if it involves a relationship (dot notation)
            if (str_contains($dbPath, '.')) {
                $relationPath = Str::beforeLast($dbPath, '.');
                $columnName   = Str::afterLast($dbPath, '.');

                try {
                    // Use JoinManager to get or create the join alias
                    $tableAlias = $this->joinManager->ensureJoin($relationPath);

                    // Create a unique alias for the result column
                    // e.g. category.sector.slug -> category_sector_slug
                    $resultAlias = str_replace('.', '_', $relationPath) . '_' . $columnName;

                    $selectColumns[] = "{$tableAlias}.{$columnName} as {$resultAlias}";
                } catch (\Exception $e) {
                    // Ignore invalid relations
                }
            } else {
                // Main table column
                $selectColumns[] = "{$mainTableAlias}.{$dbPath}";
            }
        }

        $query->select(array_unique($selectColumns));
    }

    /**
     * Perform a dynamic search with smart caching based on SQL Signature.
     *
     * @param mixed $model Class name or instance
     * @param int $cacheDuration Duration in minutes (0 to disable)
     */
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
        $columns = null, // [CHANGED] Renamed from fields to columns
        $count_only = null,
        int $cacheDuration = 0
    ) {
        $request = request();

        // 1. تجهيز المعطيات الأساسية
        $modelInstance = is_string($model) ? new $model : $model;
        $tableName = $modelInstance->getTable();
        $keyPrefix = "search_results_{$tableName}_";

        // 2. إنشاء السيرفس وتجهيز بيانات الفلترة
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
            columns: $columns, // [CHANGED] Pass columns for DB Select
            count_only: $count_only
        );

        // 3. تعريف منطق التنفيذ (Closure)
        $executionLogic = function () use ($service, $dynamicFilterData) {
            return $service->dynamicFilter($dynamicFilterData);
        };

        // 4. إذا لم يتم طلب الكاش، نفذ فوراً وأعد النتيجة
        // if ($cacheDuration <= 0) {
        //     return $executionLogic();
        // }

        // ---------------------------------------------------------
        // 5. بناء مفتاح الكاش الذكي (SQL Signature Strategy)
        // ---------------------------------------------------------

        // نقوم ببناء الكويري "مؤقتاً" لاستخراج الـ SQL
        // نمرر true للترتيب لأن تغيير الترتيب يجب أن يغير مفتاح الكاش
        $queryDraft = $service->buildQuery($dynamicFilterData, true);
        info($queryDraft->toRawSql());

        // أ. بصمة جملة الـ SQL (تتغير بتغير الفلاتر أو الكود)
        $sqlSignature = $queryDraft->toSql();

        // ب. بصمة القيم (Bindings) (تتغير بتغير قيم البحث)
        $bindingsSignature = serialize($queryDraft->getBindings());

        // ج. بصمة التصفح (Pagination) (لأن الـ Limit/Offset لا يظهران في toSql)
        $paginationSignature = json_encode([
            'page' => $dynamicFilterData->page,
            'perPage' => $dynamicFilterData->perPage,
            'format' => $dynamicFilterData->paginationFormate->value, // Enum value
            'count_only' => $dynamicFilterData->count_only
        ]);

        // المفتاح النهائي: يعتمد على الجدول + جملة الاستعلام + القيم + إعدادات الصفحة
        // مثال: search_results_items_a1b2c3d4...
        $cacheKey = $keyPrefix . md5($sqlSignature . $bindingsSignature . $paginationSignature);

        // 6. التخزين والاسترجاع من الكاش
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes($cacheDuration), $executionLogic);
    }


    /**
     * Resolves column ID for sorting ONLY. Uses JoinManager.
     */
    private static function resolveAndJoinForSort(string $columnId, JoinManager $joinManager, Model $model): ?string
    {
        if (!str_contains($columnId, '.')) {
            return $joinManager->getMainTableAlias() . '.' . $columnId;
        }

        $parts = explode('.', $columnId);
        $columnName = array_pop($parts);
        $relationPath = implode('.', $parts);

        $definedRelations = $model->defineRelationships();
        $rootRelation = $parts[0];

        if (!isset($definedRelations[$rootRelation])) {
            return null;
        }

        try {
            $finalAlias = $joinManager->ensureJoin($relationPath);
            return "{$finalAlias}.{$columnName}";
        } catch (\Exception $e) {
            return null;
        }
    }

    private static function extractAttributeIdsFromGroup(object $filterGroup): array
    {
        $attributeIds = [];

        foreach ($filterGroup->rules ?? [] as $rule) {
            if (isset($rule->condition)) {
                $attributeIds = array_merge($attributeIds, self::extractAttributeIdsFromGroup($rule));
            } elseif (isset($rule->id) && str_starts_with($rule->id, CustomAttributeFilter::ATTRIBUTE_PREFIX)) {
                $attributeIds[] = (int) str_replace(CustomAttributeFilter::ATTRIBUTE_PREFIX, '', $rule->id);
            }
        }

        return array_unique($attributeIds);
    }
}
