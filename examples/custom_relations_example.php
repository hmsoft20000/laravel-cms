<?php

/**
 * =================================================================
 * Custom Relations Example
 * =================================================================
 * 
 * This file demonstrates how to use the custom relations system
 * to add relationships to CMS models.
 */

require_once __DIR__ . '/../src/Utils/generalHelper.php';

// Example 1: Adding properties relationship to Category
echo "=== Example 1: Adding properties relationship to Category ===\n";

// Method 1: Using helper function
addCustomRelation(
    \HMsoft\Cms\Models\Shared\Category::class,
    'properties',
    [
        'type' => 'hasMany',
        'related' => \App\Models\Property::class,
        'foreign_key' => 'category_id',
        'local_key' => 'id',
    ]
);

echo "✅ Properties relationship added to Category model\n";

// Method 2: Using config file (recommended)
// Add this to config/cms_custom_relations.php:
/*
\HMsoft\Cms\Models\Shared\Category::class => [
    'properties' => [
        'type' => 'hasMany',
        'related' => \App\Models\Property::class,
        'foreign_key' => 'category_id',
        'local_key' => 'id',
    ],
],
*/

// Example 2: Adding polymorphic reviews relationship to Post
echo "\n=== Example 2: Adding polymorphic reviews relationship to Post ===\n";

addCustomRelation(
    \HMsoft\Cms\Models\Content\Post::class,
    'reviews',
    [
        'type' => 'morphMany',
        'related' => \App\Models\Review::class,
        'morph_name' => 'reviewable',
        'morph_type' => 'reviewable_type',
        'morph_id' => 'reviewable_id',
    ]
);

echo "✅ Reviews relationship added to Post model\n";

// Example 3: Adding many-to-many tags relationship
echo "\n=== Example 3: Adding many-to-many tags relationship ===\n";

addCustomRelation(
    \HMsoft\Cms\Models\Shared\Category::class,
    'tags',
    [
        'type' => 'belongsToMany',
        'related' => \App\Models\Tag::class,
        'table' => 'category_tag',
        'foreign_key' => 'category_id',
        'related_foreign_key' => 'tag_id',
        'local_key' => 'id',
        'owner_key' => 'id',
        'pivot_columns' => ['created_at', 'updated_at'],
    ]
);

echo "✅ Tags relationship added to Category model\n";

// Example 4: Checking if relationships exist
echo "\n=== Example 4: Checking relationships ===\n";

if (hasCustomRelation(\HMsoft\Cms\Models\Shared\Category::class, 'properties')) {
    echo "✅ Category has properties relationship\n";
}

if (hasCustomRelation(\HMsoft\Cms\Models\Content\Post::class, 'reviews')) {
    echo "✅ Post has reviews relationship\n";
}

// Example 5: Getting all custom relationships
echo "\n=== Example 5: Getting all custom relationships ===\n";

$categoryRelations = getCustomRelations(\HMsoft\Cms\Models\Shared\Category::class);
echo "Category custom relationships: " . json_encode(array_keys($categoryRelations), JSON_PRETTY_PRINT) . "\n";

$postRelations = getCustomRelations(\HMsoft\Cms\Models\Content\Post::class);
echo "Post custom relationships: " . json_encode(array_keys($postRelations), JSON_PRETTY_PRINT) . "\n";

// Example 6: Usage in actual code (simulated)
echo "\n=== Example 6: Usage in actual code ===\n";

echo "// Now you can use these relationships in your code:\n";
echo "\$category = Category::find(1);\n";
echo "\$properties = \$category->properties; // This will work!\n";
echo "\$tags = \$category->tags()->withPivot('created_at')->get();\n";
echo "\n\$post = Post::find(1);\n";
echo "\$reviews = \$post->reviews()->with('user')->get();\n";

echo "\n=== All examples completed successfully! ===\n";
echo "You can now use custom relationships on CMS models.\n";
echo "For more information, see: CUSTOM_RELATIONS_GUIDE.md\n";
