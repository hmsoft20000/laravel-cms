<?php

namespace HMsoft\Cms\Traits\General;

use Illuminate\Support\Facades\Schema;

/**
 * Trait IsAutoFilterable
 * Provides default implementations for the AutoFilterable interface.
 * @package HMsoft\Cms\Traits
 */
trait IsAutoFilterable
{
    protected static array $tableColumnsCache = [];
    protected bool $autoIncludeAllColumns = true;

    public function definePrimaryKeyName(): string
    {
        return $this->getKeyName();
    }

    /**
     * Default implementation. Returns columns from the main table by default.
     * The model should override this to add related filterable attributes
     * like 'translations.title' or 'categories.id'.
     */
    public function defineFilterableAttributes(): array
    {
        return $this->getCachedTableColumns($this->getTable());
    }

    /**
     * Default implementation. Returns columns from the main table by default.
     * The model should override this to add related sortable attributes.
     */
    public function defineSortableAttributes(): array
    {
        return $this->getCachedTableColumns($this->getTable());
    }

    /**
     * Default implementation. Returns columns from the main table by default.
     */
    public function defineGlobalSearchBaseAttributes(): array
    {
        return $this->getCachedTableColumns($this->getTable());
    }

    /**
     * Defines related columns for the global search.
     * The model should override this to specify which related fields to search.
     *
     * @return array Example: ['translations' => ['title', 'content'], 'categories.translations' => ['name']]
     */
    public function defineGlobalSearchRelatedAttributes(): array
    {
        return []; // Default to no related search.
    }

    /**
     * Default implementation that maps table columns directly from the main table.
     * The model should override this to add mappings for related fields like 'title' => 'translations.title'.
     */
    public function defineFieldSelectionMap(): array
    {
        $tableName = $this->getTable();
        $columns = $this->getCachedTableColumns($tableName);

        $map = [];
        foreach ($columns as $column) {
            $map[$column] = $column; // No longer needs table prefix, as service handles it.
        }
        return $map;
    }

    public function defineRelationships(): array
    {
        return [];
    }

    // --- Helper methods for caching schema ---

    protected function getCachedTableColumns(string $table): array
    {
        if (!$this->autoIncludeAllColumns) {
            return [];
        }
        
        // التحقق من الكاش الثابت (داخل نفس الـ Request)
        if (isset(self::$tableColumnsCache[$table])) {
            return self::$tableColumnsCache[$table];
        }

        // [تحسين] استخدام Cache Laravel لتخزين الهيكلية لمدة يوم كامل
        // المفتاح يعتمد على اسم الجدول
        $cacheKey = "schema_columns_{$table}";

        $finalColumns = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addDay(), function () use ($table) {
        $excludedColumns = [
                'password', 'remember_token', 'api_token', 'access_token', 
                'secret_key', 'credit_card', 'ssn', 'encrypted', 'salt'
        ];

        $columns = Schema::getColumnListing($table);
        $filteredColumns = array_diff($columns, $excludedColumns);

        $extraColumns = $this->getAdditionalColumns($table);
            return array_unique(array_merge($filteredColumns, $extraColumns));
        });

        self::$tableColumnsCache[$table] = $finalColumns;

        return $finalColumns;
    }

    protected function getAdditionalColumns(string $table): array
    {
        return []; // Hook for models to add non-schema columns if needed.
    }
}
