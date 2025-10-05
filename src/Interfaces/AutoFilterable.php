<?php

namespace HMsoft\Cms\Interfaces;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface AutoFilterable
 *
 * Defines the contract that a model must adhere to in order to be compatible
 * with the AutoFilterAndSortService. This interface ensures that the service
 * knows which columns are available for filtering, sorting, searching, and dynamic selection.
 */
interface AutoFilterable
{
    /**
     * Get the whitelist of columns that are allowed for specific, targeted filtering.
     * The service will ignore any filter requests for columns not in this list.
     *
     * Example: ['id', 'is_active', 'created_at', 'attribute_123']
     *
     * @return array A simple array of column names.
     */
    public function defineFilterableAttributes(): array;

    /**
     * Get the whitelist of columns that are allowed for sorting (ORDER BY).
     * The service will ignore any sort requests for columns not in this list.
     *
     * Example: ['title', 'created_at']
     *
     * @return array A simple array of column names.
     */
    public function defineSortableAttributes(): array;

    /**
     * Get the list of columns on the **main model's table** that are included in the global search (globalFilter).
     * The service will search for the global filter term in these columns using a 'LIKE' query.
     *
     * @return array A simple array of column names from the primary table (e.g., 'posts').
     */
    public function defineGlobalSearchBaseAttributes(): array;

    /**
     * Get the list of columns on the **translation model's table** that are included in the global search.
     * This is crucial for multi-language content searching.
     *
     * Example: ['title', 'content', 'short_content']
     *
     * @return array A simple array of column names from the translation table (e.g., 'post_translations').
     */
    public function defineGlobalSearchTranslationAttributes(): array;

    /**
     * Get the name of the translation table associated with this model.
     * If the model is not translatable, this should return null.
     *
     * Example: 'post_translations'
     *
     * @return string|null The name of the translation table, or null if not applicable.
     */
    public function defineTranslationTableName(): ?string;

    /**
     * Get the foreign key used in the translation table that links back to this model.
     * If the model is not translatable, this should return null.
     *
     * Example: 'post_id'
     *
     * @return string|null The foreign key column name.
     */
    public function defineForeignKeyInTranslationTable(): ?string;

    /**
     * Get the primary key name for the main model's table.
     * While Eloquent can often determine this, defining it here ensures consistency.
     *
     * @return string The primary key column name, typically 'id'.
     */
    public function definePrimaryKeyName(): string;

    /**
     * Get the column name that stores the language identifier (e.g., 'en', 'ar') in the translation table.
     *
     * @return string|null The locale column name, typically 'locale'.
     */
    public function defineLocaleColumnName(): ?string;

    /**
     * Allows for custom logic to be applied when joining the translation table.
     * This is an advanced feature for scenarios requiring more complex JOIN conditions
     * than a simple foreign key match. The default implementation in the IsAutoFilterable
     * trait handles the standard case.
     *
     * @param Builder $query The Eloquent query builder instance.
     * @param string $mainTable The name of the model's main table (e.g., 'posts').
     * @param string $translationTable The name of the model's translation table (e.g., 'post_translations').
     * @return void
     */
    public function defineTranslationJoin(Builder $query, string $mainTable, string $translationTable): void;

    /**
     * Get the map of API-friendly field names to their corresponding database columns.
     * This is the core of the dynamic 'SELECT' feature for performance optimization.
     * The key is the field name used in the API request ('fields=...'), and the value
     * is the fully qualified database column name (e.g., 'table.column').
     *
     * Example:
     * [
     * 'id' => 'posts.id',
     * 'is_active' => 'posts.is_active',
     * 'title' => 'post_translations.title'
     * ]
     *
     * @return array An associative array mapping API fields to database columns.
     */
    public function defineFieldSelectionMap(): array;


    /**
     * Defines the model's relationships that can be joined by the service.
     * The key is the API name (e.g., 'category'), value is the Eloquent method name (e.g., 'categories').
     *
     * @return array
     */
    public function defineRelationships(): array;
}
