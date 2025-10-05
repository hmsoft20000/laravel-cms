<?php

namespace HMsoft\Cms\Traits\General;

use Illuminate\Http\Request;

/**
 * Trait for handling owner field mapping
 * 
 * This trait provides methods to automatically map frontend field names
 * (like portfolio_id, blog_id) to the expected owner_id field for polymorphic relationships.
 */
trait HandlesOwnerFieldMapping
{
    /**
     * Get the owner field name for the current content type
     */
    protected function getOwnerFieldName(string $contentType): string
    {
        return config("cms.owner_field_mapping.{$contentType}", 'owner_id');
    }

    /**
     * Get the model class for the content type
     */
    protected function getModelClass(string $contentType): ?string
    {
        return config("cms.morph_map.{$contentType}");
    }

    /**
     * Get the table name for the content type
     */
    protected function getTableName(string $contentType): string
    {
        $modelClass = $this->getModelClass($contentType);
        return $modelClass ? (new $modelClass)->getTable() : 'posts';
    }

    /**
     * Get all allowed owner types from morph map
     */
    protected function getAllowedOwnerTypes(): array
    {
        $allowedTypes = array_keys(config('cms.morph_map', []));
        $allowedTypes[] = 'post'; // Always allow 'post' for backward compatibility
        return $allowedTypes;
    }

    /**
     * Map request data to use owner_id instead of content-specific field names
     */
    protected function mapOwnerField(Request $request, string $contentType): array
    {
        $data = $request->all();
        $ownerField = $this->getOwnerFieldName($contentType);
        
        // If the content-specific field exists, map it to owner_id
        if (isset($data[$ownerField])) {
            $data['owner_id'] = $data[$ownerField];
            unset($data[$ownerField]);
        }
        
        return $data;
    }

    /**
     * Get the owner ID from request data
     */
    protected function getOwnerIdFromRequest(Request $request, string $contentType): ?int
    {
        $ownerField = $this->getOwnerFieldName($contentType);
        return $request->input($ownerField);
    }

    /**
     * Validate that the owner field is present in the request
     */
    protected function validateOwnerField(Request $request, string $contentType): void
    {
        $ownerField = $this->getOwnerFieldName($contentType);
        
        if (!$request->has($ownerField)) {
            throw new \InvalidArgumentException("The {$ownerField} field is required.");
        }
    }

    /**
     * Get the content type from the route
     */
    protected function getContentTypeFromRoute(): string
    {
        return request()->route('type', 'posts');
    }

    /**
     * Map request data for the current content type
     */
    protected function mapRequestData(Request $request): array
    {
        $contentType = $this->getContentTypeFromRoute();
        return $this->mapOwnerField($request, $contentType);
    }

    /**
     * Get dynamic filter key map for all content types
     */
    protected function getDynamicFilterKeyMap(): array
    {
        $filterKeyMap = [];
        $ownerFieldMapping = config('cms.owner_field_mapping', []);
        
        foreach ($ownerFieldMapping as $type => $field) {
            $filterKeyMap[$field] = 'owner_id';
        }
        
        return $filterKeyMap;
    }

    /**
     * Get filter key map for specific content type
     */
    protected function getFilterKeyMapForType(string $contentType): array
    {
        $ownerField = $this->getOwnerFieldName($contentType);
        return [$ownerField => 'owner_id'];
    }
}
