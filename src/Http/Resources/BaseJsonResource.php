<?php

namespace HMsoft\Cms\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseJsonResource extends JsonResource
{
    protected ?string $selectedFields = null;
    protected ?string $excludedFields = null;

    public function withFields(?string $fields): static
    {
        $this->selectedFields = $fields;
        return $this;
    }

    public function withExceptFields(?string $fields): static
    {
        $this->excludedFields = $fields;
        return $this;
    }

    public function toArray($request)
    {
        // Resolve the data first
        $data = method_exists($this, 'resolveData')
            ? $this->resolveData($request)
            : \Illuminate\Http\Resources\Json\JsonResource::toArray($request);

        // Process and format translations automatically
        if (isset($data['translations']) && is_a($data['translations'], Collection::class)) {
            $data['translations'] = $this->formatTranslations($data['translations']);
        }

        // Add translated fields to the top level of the array
        $data = $this->processTranslations($data);

        // Filter fields based on selection or exclusion, supporting unlimited recursion
        if (!is_null($this->selectedFields) && trim($this->selectedFields) !== '') {
            return $this->filterDataRecursively($data, $this->selectedFields);
        }

        if (!is_null($this->excludedFields) && trim($this->excludedFields) !== '') {
            return $this->filterExceptDataRecursively($data, $this->excludedFields);
        }

        return $data;
    }

    protected function formatTranslations(Collection|null $translations = null): array
    {
        if (is_null($translations) || $translations->isEmpty()) {
            return [];
        }

        return $translations
            ->groupBy('locale')
            ->map(function ($group) {
                return $group->first()->toArray();
            })->all();
    }

    protected function processTranslations(array $data): array
    {
        if (empty($data['translations']) || !is_array($data['translations'])) {
            return $data;
        }

        $currentLocale = app()->getLocale();
        $allTranslatableFields = [];

        foreach ($data['translations'] as $locale => $translation) {
            if (is_array($translation)) {
                $allTranslatableFields = array_merge($allTranslatableFields, array_keys($translation));
            }
        }
        $allTranslatableFields = array_unique($allTranslatableFields);

        foreach ($data['translations'] as $locale => &$translation) {
            foreach ($allTranslatableFields as $field) {
                if (!isset($translation[$field])) {
                    $translation[$field] = null;
                }
            }
        }

        $ignoredKeys = ['id', 'locale', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

        foreach ($allTranslatableFields as $field) {
            if (in_array($field, $ignoredKeys)) {
                continue;
            }

            // Try to fill from current locale, then fallback to any other locale
            if (!array_key_exists($field, $data) || empty($data[$field])) {
                if (isset($data['translations'][$currentLocale]) && array_key_exists($field, $data['translations'][$currentLocale]) && !empty($data['translations'][$currentLocale][$field])) {
                    $data[$field] = $data['translations'][$currentLocale][$field];
                } else {
                    foreach ($data['translations'] as $locale => $trans) {
                        if ($locale !== $currentLocale && array_key_exists($field, $trans) && !empty($trans[$field])) {
                            $data[$field] = $trans[$field];
                            break;
                        }
                    }
                }
            }
        }

        // Ensure every translatable field exists at top-level even if null
        foreach ($allTranslatableFields as $field) {
            if (in_array($field, $ignoredKeys)) {
                continue;
            }
            if (!array_key_exists($field, $data)) {
                $data[$field] = null;
            }
        }

        return $data;
    }

    /**
     * Recursively filters an array based on selected fields.
     */
    protected function filterDataRecursively(array $data, string $fieldsString): array
    {
        $fields = explode(',', $fieldsString);
        $result = [];

        // Group fields by their root key to handle multiple selections on the same nested path
        $groupedFields = [];
        foreach ($fields as $field) {
            $parts = explode('.', $field);
            $rootKey = $parts[0];
            if (!isset($groupedFields[$rootKey])) {
                $groupedFields[$rootKey] = [];
            }
            $groupedFields[$rootKey][] = $parts;
        }

        // Process each group
        foreach ($groupedFields as $rootKey => $fieldGroups) {
            $this->setNestedValueGrouped($result, $data, $rootKey, $fieldGroups);
        }

        return $result;
    }

    /**
     * Helper function to set nested values for grouped field selectors.
     */
    protected function setNestedValueGrouped(array &$target, array $source, string $rootKey, array $fieldGroups): void
    {
        // If the root key doesn't exist in source, skip it
        if (!array_key_exists($rootKey, $source) || is_null($source[$rootKey])) {
            return;
        }

        $value = $source[$rootKey];
        $value = $this->transformValue($value);

        // Check if this is a simple field (no dots in any field group)
        $hasNestedFields = false;
        foreach ($fieldGroups as $parts) {
            if (count($parts) > 1) {
                $hasNestedFields = true;
                break;
            }
        }

        if (!$hasNestedFields) {
            // Simple field selection - just set the value
            $target[$rootKey] = $value;
            return;
        }

        // Handle nested field selections
        if (is_array($value)) {
            // If value is a numeric array (list of objects)
            if (array_keys($value) === range(0, count($value) - 1)) {
                $target[$rootKey] = [];
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $itemResult = [];
                        // Apply each field selector to this item
                        foreach ($fieldGroups as $parts) {
                            // Remove the root key to get the remaining path
                            $remainingParts = array_slice($parts, 1);
                            if (!empty($remainingParts)) {
                                $this->setNestedValue($itemResult, $item, $remainingParts);
                            }
                        }
                        if (!empty($itemResult)) {
                            $target[$rootKey][] = $itemResult;
                        }
                    }
                }
            } else {
                // Value is an associative array (object) - not an array of objects
                // This shouldn't happen for nested field selection on arrays, but handle it anyway
                $target[$rootKey] = [];
                foreach ($fieldGroups as $parts) {
                    $remainingParts = array_slice($parts, 1);
                    if (!empty($remainingParts)) {
                        $this->setNestedValue($target[$rootKey], $value, $remainingParts);
                    }
                }
            }
        }
    }

    /**
     * Helper function to set a nested value based on a dot-separated key.
     */
    protected function setNestedValue(array &$target, $source, array $parts): void
    {
        $key = array_shift($parts);

        // If the key doesn't exist in the source or is null, skip it.
        if (!array_key_exists($key, $source) || is_null($source[$key])) {
            return;
        }

        $value = $source[$key];

        if (empty($parts)) {
            // End of the dot-separated key, set the final value.
            $target[$key] = $this->transformValue($value);
            return;
        }

        // Handle nested resources or collections for the next level of recursion.
        if ($value instanceof JsonResource) {
            $value = $value->toArray(request());
        } elseif ($value instanceof ResourceCollection || $value instanceof Collection) {
            $value = $value->toArray();
        }

        // If the value is not an array, we can't recurse further.
        if (!is_array($value)) {
            return;
        }

        // Initialize the target nested array if it doesn't exist.
        if (!isset($target[$key])) {
            $target[$key] = [];
        }

        // If the value is a numeric array (list of objects), loop through it.
        if (array_keys($value) === range(0, count($value) - 1)) {
            $target[$key] = [];
            foreach ($value as $item) {
                if (is_array($item)) {
                    $nestedResult = [];
                    $this->setNestedValue($nestedResult, $item, $parts);
                    if (!empty($nestedResult)) {
                        $target[$key][] = $nestedResult;
                    }
                }
            }
        } else {
            // Recursive call for the next level.
            $this->setNestedValue($target[$key], $value, $parts);
        }
    }

    /**
     * Recursively filters an array based on excluded fields.
     */
    protected function filterExceptDataRecursively(array $data, string $fieldsString): array
    {
        $fields = explode(',', $fieldsString);
        $result = $this->transformValue($data);

        foreach ($fields as $field) {
            $parts = explode('.', $field);
            $this->unsetNested($result, $parts);
        }

        return $result;
    }

    /**
     * Helper function to unset a nested value based on a dot-separated key.
     */
    protected function unsetNested(array &$array, array $keys): void
    {
        $currentKey = array_shift($keys);

        // If the key doesn't exist, we can't do anything.
        if (!isset($array[$currentKey])) {
            return;
        }

        // Convert the value to an array if it's a resource or collection before processing
        $array[$currentKey] = $this->transformValue($array[$currentKey]);

        // If there are no more keys, we've reached the end of the path.
        if (empty($keys)) {
            unset($array[$currentKey]);
            return;
        }

        // Get a reference to the nested value.
        $nested = &$array[$currentKey];

        // If the nested value is an array, we can go deeper.
        if (is_array($nested)) {
            // If it's a numeric array (list)
            if (array_keys($nested) === range(0, count($nested) - 1)) {
                foreach ($nested as &$item) {
                    $this->unsetNested($item, $keys);
                }
            } else {
                // If it's an associative array
                $this->unsetNested($nested, $keys);
            }
        }
    }

    /**
     * Transforms the value to an array if it's a JsonResource or Collection.
     */
    protected function transformValue($value)
    {
        if ($value instanceof JsonResource) {
            return $value->toArray(request());
        } elseif ($value instanceof ResourceCollection || $value instanceof Collection) {
            return $value->toArray();
        }

        return $value;
    }
}
