<?php

use HMsoft\Cms\Models\BusinessSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


if (!function_exists('getFillableList')) {
    /**
     * get fillable properties from model.
     * @param Model $model
     */
    function getFillableList(Model $model)
    {
        return $model->getFillable();
    }
}

if (!function_exists('getColumnListing')) {
    /**
     * get fillable properties from model.
     * @param Model $model
     */
    function getColumnListing(Model $model)
    {
        return Schema::getColumnListing($model->getTable());
    }
}


if (!function_exists('arrayOfObjectFirst')) {
    /**
     * Return a filter object from array of objects by key.
     */
    function arrayOfObjectFirst($filtersArray, $objectKey, $arrayKey = "id")
    {
        return $filtersArray->first(function ($filter) use ($objectKey, $arrayKey) {
            if (gettype($filter) == "object") {
                return $filter->{$arrayKey} == $objectKey;
            } else {
                return $filter[$arrayKey] == $objectKey;
            }
        });
    }
}

if (!function_exists('newUUID')) {
    /**
     * generate uuid.
     */
    function newUUID()
    {
        return Str::uuid()->toString();
    }
}

if (!function_exists('getFillableColumns')) {
    /**
     * get can fillable columns from model.
     * @param Model $model
     * @return array<string>
     */
    function getFillableColumns(Model $model)
    {
        collect(getColumnListing($model))->filter(function ($column) use ($model) {
            return  $model->isFillable($column);
        });
    }
}


if (!function_exists('getColumnListing')) {
    /**
     * get fillable properties from model.
     * @param Model $model
     */
    function getColumnListing(Model $model)
    {
        return Schema::getColumnListing($model->getTable());
    }
}


if (!function_exists('hasColumn')) {
    /**
     * get fillable properties from model.
     * @param Model $model
     */
    function hasColumn(Model $model, $columnName): bool
    {
        return Schema::hasColumn($model->getTable(), $columnName);
    }
}

if (!function_exists('getSlug')) {
    /**
     * get Slug From Text.
     * @param Model $model
     */
    function getSlug(
        $title,
        $separator = '-',
        $language = 'en',
        $dictionary = ['@' => 'at']
    ): string {
        return Str::slug(title: $title, separator: $separator, language: $language, dictionary: $dictionary);
    }
}


if (!function_exists('get_settings')) {
    function get_settings($object, $type)
    {
        $config = null;
        foreach ($object as $setting) {
            if ($setting['type'] == $type) {
                $config = $setting;
            }
        }
        return $config;
    }
}

if (!function_exists('getSettingsByName')) {
    function getSettingsByName($type)
    {
        return BusinessSetting::where('type', $type)->first()?->value;
    }
}

if (!function_exists('myPaginate')) {

    function myPaginate(
        $items,
        $perPage = null,
        $page = null,
        $baseUrl = null,
        $options = []
    ) {
        $perPage = $perPage ?? cmsPagination('default_data_limit');
        $items = $items instanceof Collection ?
            $items : Collection::make($items);

        if ($perPage == 'all' || $page == 'all') {
            $perPage =  $items->count();
            $page = 1;
        }

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $lap = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            max($perPage, 1),
            $page,
            $options
        );

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;
    }
}

if (!function_exists('myStripTags')) {
    function myStripTags($html)
    {
        return  strip_tags(str_replace('&nbsp;', ' ', $html));
    }
}

if (!function_exists('cmsConfig')) {
    /**
     * Get CMS configuration value with dot notation support.
     * 
     * @param string $key The configuration key (e.g., 'pagination.default_data_limit')
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    function cmsConfig($key, $default = null)
    {
        return config("cms_constants.{$key}", $default);
    }
}

if (!function_exists('cmsImageDir')) {
    /**
     * Get CMS image directory name for a specific type.
     * 
     * @param string $type The image type (e.g., 'users', 'blog', 'portfolio')
     * @return string
     */
    function cmsImageDir($type)
    {
        return cmsConfig("image_directories.{$type}", $type);
    }
}

if (!function_exists('cmsPagination')) {
    /**
     * Get CMS pagination setting.
     * 
     * @param string $key The pagination key ('default_data_limit' or 'default_page')
     * @return mixed
     */
    function cmsPagination($key)
    {
        return cmsConfig("pagination.{$key}");
    }
}

if (!function_exists('cmsFileSetting')) {
    /**
     * Get CMS file setting.
     * 
     * @param string $key The file setting key
     * @return mixed
     */
    function cmsFileSetting($key)
    {
        return cmsConfig("files.{$key}");
    }
}

if (!function_exists('cmsContentSetting')) {
    /**
     * Get CMS content setting.
     * 
     * @param string $key The content setting key
     * @return mixed
     */
    function cmsContentSetting($key)
    {
        return cmsConfig("content.{$key}");
    }
}

if (!function_exists('addCustomRelation')) {
    /**
     * Add a custom relationship to a CMS model.
     * 
     * @param string $modelClass The model class name
     * @param string $relationName The relationship name
     * @param array $config The relationship configuration
     * @return void
     */
    function addCustomRelation(string $modelClass, string $relationName, array $config)
    {
        $customRelations = config('cms.custom_relations', []);
        $customRelations[$modelClass][$relationName] = $config;
        config(['cms.custom_relations' => $customRelations]);
        
        // Add the relation to the model if it's already loaded
        if (class_exists($modelClass)) {
            $modelClass::addCustomRelation($relationName, $config);
        }
    }
}

if (!function_exists('hasCustomRelation')) {
    /**
     * Check if a model has a custom relationship.
     * 
     * @param string $modelClass The model class name
     * @param string $relationName The relationship name
     * @return bool
     */
    function hasCustomRelation(string $modelClass, string $relationName): bool
    {
        $customRelations = config('cms.custom_relations', []);
        return isset($customRelations[$modelClass][$relationName]);
    }
}

if (!function_exists('getCustomRelations')) {
    /**
     * Get all custom relationships for a model.
     * 
     * @param string $modelClass The model class name
     * @return array
     */
    function getCustomRelations(string $modelClass): array
    {
        $customRelations = config('cms.custom_relations', []);
        return $customRelations[$modelClass] ?? [];
    }
}

if (!function_exists('getExtendedModelClass')) {
    /**
     * Get the extended model class for a CMS model.
     * 
     * @param string $originalModelClass The original CMS model class
     * @return string|null
     */
    function getExtendedModelClass(string $originalModelClass): ?string
    {
        return \HMsoft\Cms\Services\ModelExtensionService::getExtendedModelClass($originalModelClass);
    }
}

if (!function_exists('hasExtendedModel')) {
    /**
     * Check if a CMS model has an extended version.
     * 
     * @param string $originalModelClass The original CMS model class
     * @return bool
     */
    function hasExtendedModel(string $originalModelClass): bool
    {
        return \HMsoft\Cms\Services\ModelExtensionService::hasExtendedModel($originalModelClass);
    }
}

if (!function_exists('getOriginalModelClass')) {
    /**
     * Get the original model class from an extended model.
     * 
     * @param string $extendedModelClass The extended model class
     * @return string|null
     */
    function getOriginalModelClass(string $extendedModelClass): ?string
    {
        $extendedModels = config('cms.extended_models', []);
        
        foreach ($extendedModels as $original => $extended) {
            if ($extended === $extendedModelClass) {
                return $original;
            }
        }
        
        return null;
    }
}

if (!function_exists('registerExtendedModel')) {
    /**
     * Register an extended model for a CMS model.
     * 
     * @param string $originalModelClass The original CMS model class
     * @param string $extendedModelClass The extended model class
     * @return void
     */
    function registerExtendedModel(string $originalModelClass, string $extendedModelClass): void
    {
        $extendedModels = config('cms.extended_models', []);
        $extendedModels[$originalModelClass] = $extendedModelClass;
        config(['cms.extended_models' => $extendedModels]);
        
        // Bind the extended model in the container
        app()->bind($originalModelClass, $extendedModelClass);
        app()->bind($extendedModelClass, $extendedModelClass);
    }
}

if (!function_exists('resolveExtendedModel')) {
    /**
     * Resolve an extended model instance from the container.
     * 
     * @param string $originalModelClass The original CMS model class
     * @return mixed
     */
    function resolveExtendedModel(string $originalModelClass)
    {
        return \HMsoft\Cms\Services\ModelExtensionService::resolve($originalModelClass);
    }
}
