<?php

namespace HMsoft\Cms\Interfaces;

/**
 * [EN] Interface AutoFilterable
 *
 * This interface is the essential contract that a model must implement to become compatible
 * with the advanced `AutoFilterAndSortService`. It acts as a "control panel" for each model,
 * giving the developer explicit control over how the service can interact with its data and relationships.
 * ---
 * [AR] واجهة AutoFilterable
 *
 * هذه الواجهة هي العقد الأساسي الذي يجب على أي موديل تطبيقه ليصبح متوافقًا مع خدمة الفلترة
 * والفرز المتقدمة `AutoFilterAndSortService`. هي بمثابة "لوحة تحكم" لكل موديل، تمنح المطور
 * تحكمًا صريحًا في كيفية تفاعل الخدمة مع بياناته وعلاقاته.
 */
interface AutoFilterable
{
    /**
     * [EN] Defines the model's relationships that the service is allowed to join.
     * This is the gateway for all cross-table operations.
     * - **Key:** The public-facing API name for the relationship (e.g., 'categories').
     * - **Value:** The actual Eloquent method name in the model (e.g., 'categories', 'authorDetails').
     *
     * [AR] تحدد علاقات الموديل المسموح للخدمة بربطها (Join).
     * هذه هي البوابة لكل العمليات التي تتطلب جداول متعددة.
     * - **المفتاح (Key):** اسم العلاقة العام المستخدم في الـ API (مثل 'categories').
     * - **القيمة (Value):** اسم دالة العلاقة الفعلي في الموديل (مثل 'categories', 'authorDetails').
     *
     * @return array An associative array mapping API names to Eloquent method names.
     *
     * ### Example / مثال:
     * ```php
     * public function defineRelationships(): array
     * {
     * return [
     * 'categories' => 'categories',
     * 'author'     => 'authorDetails',
     * ];
     * }
     * ```
     */
    public function defineRelationships(): array;

    /**
     * [EN] Defines the security whitelist of attributes that are allowed for filtering.
     * The service will ignore any filter requests for attributes not in this list.
     * Use dot-notation for related attributes.
     *
     * [AR] تحدد القائمة البيضاء الأمنية للحقول المسموح بالفلترة عليها.
     * الخدمة ستتجاهل أي طلب فلترة لحقل غير موجود في هذه القائمة.
     * استخدم صيغة النقطة للوصول لحقول العلاقات.
     *
     * @return array A simple array of filterable attribute names.
     *
     * ### Example / مثال:
     * ```php
     * public function defineFilterableAttributes(): array
     * {
     * return [
     * 'is_active',
     * 'categories.id',
     * 'author.status',
     * ];
     * }
     * ```
     */
    public function defineFilterableAttributes(): array;

    /**
     * [EN] Defines the security whitelist of attributes that are allowed for sorting (ORDER BY).
     * This prevents unwanted or slow sorting on unindexed columns.
     *
     * [AR] تحدد القائمة البيضاء الأمنية للحقول المسموح بالفرز عليها (ORDER BY).
     * هذا يمنع الفرز غير المرغوب فيه أو البطيء على أعمدة غير مفهرسة.
     *
     * @return array A simple array of sortable attribute names.
     *
     * ### Example / مثال:
     * ```php
     * public function defineSortableAttributes(): array
     * {
     * return [
     * 'created_at',
     * 'translations.title',
     * ];
     * }
     * ```
     */
    public function defineSortableAttributes(): array;

    /**
     * [EN] Defines the map of API-friendly field names to their database column paths.
     * This acts as a translator for the `fields` API parameter, creating a clean abstraction layer.
     * - **Key:** The public API field name (e.g., 'title').
     * - **Value:** The database column name or a dot-notation path (e.g., 'translations.title').
     *
     * [AR] تحدد خريطة ربط أسماء الحقول الصديقة للـ API بمساراتها في قاعدة البيانات.
     * تعمل كمترجم للـ `fields` parameter في الـ API، مما ينشئ طبقة تجريد نظيفة.
     * - **المفتاح (Key):** اسم الحقل العام في الـ API (مثل 'title').
     * - **القيمة (Value):** اسم عمود قاعدة البيانات أو مسار بصيغة النقطة (مثل 'translations.title').
     *
     * @return array An associative array mapping API fields to database columns/paths.
     *
     * ### Example / مثال:
     * ```php
     * public function defineFieldSelectionMap(): array
     * {
     * return [
     * 'status'      => 'is_active',
     * 'title'       => 'translations.title',
     * 'author_name' => 'author.name',
     * ];
     * }
     * ```
     */
    public function defineFieldSelectionMap(): array;

    /**
     * [EN] Defines the list of columns on the **main model's table only** for the global search.
     *
     * [AR] تحدد قائمة الأعمدة الموجودة في **الجدول الأساسي للموديل فقط** للبحث الشامل.
     *
     * @return array A simple array of column names from the primary table.
     *
     * ### Example / مثال:
     * ```php
     * public function defineGlobalSearchBaseAttributes(): array
     * {
     * return ['slug', 'internal_code'];
     * }
     * ```
     */
    public function defineGlobalSearchBaseAttributes(): array;

    /**
     * [EN] Specifies the primary key column name for the model's table.
     * Crucial for the `GROUP BY` clause to prevent duplicate results after joins.
     *
     * [AR] تحدد اسم حقل المفتاح الأساسي لجدول الموديل.
     * ضرورية لتطبيق جملة `GROUP BY` بشكل صحيح لمنع تكرار النتائج بعد عمليات الربط.
     *
     * @return string The primary key column name, typically 'id'.
     *
     * ### Example / مثال:
     * ```php
     * public function definePrimaryKeyName(): string
     * {
     * return 'uuid'; // If the primary key is not 'id'
     * }
     * ```
     */
    public function definePrimaryKeyName(): string;
}
