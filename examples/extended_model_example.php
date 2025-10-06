<?php

/**
 * =================================================================
 * Extended Model Example
 * =================================================================
 * 
 * This file demonstrates how to create and use extended models
 * that extend CMS models, similar to Laravel's model extension.
 */

// Example 1: Create an extended Category model
echo "=== Example 1: Creating Extended Category Model ===\n";

// First, create the extended model using Artisan command
echo "Run: php artisan cms:make-extended-model Category\n";
echo "This will create: app/Models/Category.php\n\n";

// Example 2: The generated extended model content
echo "=== Example 2: Generated Extended Model Content ===\n";

$extendedModelContent = '<?php

namespace App\Models;

use HMsoft\Cms\Models\Shared\Category;

/**
 * Extended Category Model
 * 
 * This model extends the CMS Category model and allows you to add
 * custom relationships, methods, scopes, and properties.
 */
class Category extends \HMsoft\Cms\Models\Shared\Category
{
    // =================================================================
    // CUSTOM RELATIONSHIPS
    // =================================================================
    
    /**
     * Add properties relationship
     */
    public function properties()
    {
        return $this->hasMany(Property::class, \'category_id\');
    }
    
    /**
     * Add tags relationship
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, \'category_tag\', \'category_id\', \'tag_id\');
    }
    
    /**
     * Add products relationship
     */
    public function products()
    {
        return $this->hasMany(Product::class, \'category_id\');
    }

    // =================================================================
    // CUSTOM METHODS
    // =================================================================
    
    /**
     * Get active properties
     */
    public function getActiveProperties()
    {
        return $this->properties()->where(\'is_active\', true)->get();
    }
    
    /**
     * Get category with all relationships
     */
    public function getFullCategory()
    {
        return $this->with([\'properties\', \'tags\', \'products\'])->first();
    }

    // =================================================================
    // CUSTOM SCOPES
    // =================================================================
    
    /**
     * Scope for categories with properties
     */
    public function scopeWithProperties($query)
    {
        return $query->whereHas(\'properties\');
    }
    
    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where(\'is_active\', true);
    }

    // =================================================================
    // CUSTOM ACCESSORS & MUTATORS
    // =================================================================
    
    /**
     * Custom accessor for full name
     */
    public function getFullNameAttribute()
    {
        return $this->name . \' (\' . $this->id . \')\';
    }
    
    /**
     * Custom mutator for name
     */
    public function setNameAttribute($value)
    {
        $this->attributes[\'name\'] = strtoupper($value);
    }
}';

echo $extendedModelContent . "\n\n";

// Example 3: Register the extended model in config
echo "=== Example 3: Register Extended Model in Config ===\n";

$configContent = '<?php

return [
    // Register the extended model
    \HMsoft\Cms\Models\Shared\Category::class => \App\Models\Category::class,
    
    // You can add more extended models
    \HMsoft\Cms\Models\Content\Post::class => \App\Models\Post::class,
    \HMsoft\Cms\Models\Sector\Sector::class => \App\Models\Sector::class,
];';

echo "Add to config/cms_extended_models.php:\n";
echo $configContent . "\n\n";

// Example 4: Usage in actual code
echo "=== Example 4: Usage in Actual Code ===\n";

$usageExample = '// Now you can use the extended model with all custom functionality!

// Get category with properties
$category = Category::find(1);
$properties = $category->properties; // This will work!

// Use custom methods
$activeProperties = $category->getActiveProperties();
$fullCategory = $category->getFullCategory();

// Use custom scopes
$categoriesWithProperties = Category::withProperties()->get();
$activeCategories = Category::active()->get();

// Use custom accessors
$fullName = $category->full_name; // Custom accessor

// Use custom mutators
$category->name = "test"; // Will be converted to "TEST"

// All original CMS functionality still works
$translations = $category->translations;
$sector = $category->sector;';

echo $usageExample . "\n\n";

// Example 5: Benefits
echo "=== Example 5: Benefits of Extended Models ===\n";

$benefits = [
    "✅ Full control over the model",
    "✅ Add custom relationships easily",
    "✅ Add custom methods and scopes",
    "✅ Add custom accessors and mutators",
    "✅ Override existing methods if needed",
    "✅ Keep original CMS functionality",
    "✅ No modification of package files",
    "✅ IDE autocomplete support",
    "✅ Type safety and validation",
    "✅ Easy to maintain and update",
];

foreach ($benefits as $benefit) {
    echo $benefit . "\n";
}

echo "\n=== Extended Models System Complete! ===\n";
echo "This approach is much more powerful and flexible than the previous method!\n";
echo "It follows Laravel\'s best practices and gives you complete control.\n";
