<?php

namespace HMsoft\Cms\Traits\General;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

/**
 * Trait IsAutoFilterable
 *
 * Provides default implementations for the AutoFilterable interface,
 * enabling models to be easily used with the AutoFilterAndSortService.
 *
 * @package HMsoft\Cms\Traits
 */
trait IsAutoFilterable
{

    protected static $tableColumnsCache = [];

    /**
     * Enable automatic inclusion of all columns (except sensitive ones)
     * @var bool
     */
    protected $autoIncludeAllColumns = true;

    /**
     * Get the primary key name for the main model's table.
     * This implementation uses Laravel's built-in method, assuming the default is 'id'.
     *
     * @return string
     */
    public function definePrimaryKeyName(): string
    {
        return $this->getKeyName();
    }

    /**
     * Get the column name for the locale in the translation table.
     * This implementation assumes the column is named 'locale'.
     *
     * @return string|null
     */
    public function defineLocaleColumnName(): ?string
    {
        // If your model had a constant like `LOCALE_KEY`, you could use it.
        // Otherwise, returning 'locale' is a safe and standard default.
        return 'locale';
    }

    /**
     * Applies the standard translation join logic to the query builder.
     * This method is called by the service to automatically join the translation table.
     * A model can override this method if it requires a more complex join.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param string $mainTable The name of the model's main table (e.g., 'property').
     * @param string $translationTable The name of the model's translation table (e.g., 'property_translations').
     * @return void
     */
    public function defineTranslationJoin(Builder $query, string $mainTable, string $translationTable): void
    {
        $translationForeignKey = $this->defineForeignKeyInTranslationTable();
        $modelKeyName = $this->definePrimaryKeyName();
        $localeColumn = $this->defineLocaleColumnName();
        $currentLocale = app()->getLocale();

        // Standard JOIN clause to link the main table with its translation table
        // for the current application language.
        $query->join($translationTable, function ($join) use ($translationTable, $translationForeignKey, $mainTable, $modelKeyName, $localeColumn, $currentLocale) {
            $join->on("{$translationTable}.{$translationForeignKey}", '=', "{$mainTable}.{$modelKeyName}")
                ->where("{$translationTable}.{$localeColumn}", $currentLocale);
        });
    }

    /**
     * Default implementation for defineFilterableAttributes.
     * Returns an empty array by default, forcing the model to define its own list.
     *
     * @return array
     */
    public function defineFilterableAttributes(): array
    {
        $columns = $this->getCachedTableColumns($this->getTable());
        $translationTable = $this->defineTranslationTableName();

        if ($translationTable) {
            $translationColumns = $this->getCachedTableColumns($translationTable);
            $columns = array_merge($columns, $translationColumns);
        }

        return $columns;
    }

    /**
     * Default implementation for defineSortableAttributes.
     * Returns an empty array by default.
     *
     * @return array
     */
    public function defineSortableAttributes(): array
    {
        $columns = $this->getCachedTableColumns($this->getTable());
        $translationTable = $this->defineTranslationTableName();

        if ($translationTable) {
            $translationColumns = $this->getCachedTableColumns($translationTable);
            $columns = array_merge($columns, $translationColumns);
        }

        return $columns;
    }

    /**
     * Default implementation for defineGlobalSearchBaseAttributes.
     * Returns an empty array by default.
     *
     * @return array
     */
    public function defineGlobalSearchBaseAttributes(): array
    {
        return $this->getCachedTableColumns($this->getTable());
    }

    /**
     * Default implementation for defineGlobalSearchTranslationAttributes.
     * Returns an empty array by default.
     *
     * @return array
     */
    public function defineGlobalSearchTranslationAttributes(): array
    {
        $translationTable = $this->defineTranslationTableName();
        return $translationTable ? $this->getCachedTableColumns($translationTable) : [];
    }

    /**
     * Default implementation for defineTranslationTableName.
     * Returns null, indicating the model is not translatable by default.
     * A translatable model MUST override this method.
     *
     * @return string|null
     */
    public function defineTranslationTableName(): ?string
    {
        return null;
    }

    /**
     * Default implementation for defineForeignKeyInTranslationTable.
     * Returns null by default.
     * A translatable model MUST override this method.
     *
     * @return string|null
     */
    public function defineForeignKeyInTranslationTable(): ?string
    {
        return null;
    }

    /**
     * Get cached table columns with sensitive fields excluded.
     *
     * @param string $table
     * @return array
     */
    protected function getCachedTableColumns(string $table): array
    {
        if (!$this->autoIncludeAllColumns) {
            return [];
        }
        if (isset(self::$tableColumnsCache[$table])) {
            return self::$tableColumnsCache[$table];
        }

        $excludedColumns = [
            'password',
            'remember_token',
            'api_token',
            'access_token',
            'secret_key',
            'credit_card',
            'ssn',
            'encrypted',
            'salt'
        ];

        $columns = Schema::getColumnListing($table);
        $filteredColumns = array_diff($columns, $excludedColumns);

        $extraColumns = $this->getAdditionalColumns($table);

        $finalColumns = array_unique(array_merge($filteredColumns, $extraColumns));

        self::$tableColumnsCache[$table] = $filteredColumns;

        return $finalColumns;
    }

    protected function getAdditionalColumns(string $table): array
    {
        return [];
    }

    /**
     * Default implementation that maps table columns directly.
     * Example: ['id' => 'posts.id', 'is_active' => 'posts.is_active']
     *
     * @return array
     */
    public function defineFieldSelectionMap(): array
    {
        $tableName = $this->getTable();
        $columns = $this->getCachedTableColumns($tableName);

        $map = [];
        foreach ($columns as $column) {
            $map[$column] = "{$tableName}.{$column}";
        }
        return $map;
    }

    /**
     * Default implementation for definable relationships.
     * @return array
     */
    public function defineRelationships(): array
    {
        return [];
    }
}
