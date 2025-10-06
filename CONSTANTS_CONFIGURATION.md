# CMS Constants Configuration

This document explains how to configure CMS constants through the config file system, allowing end-developers to customize values without modifying package code.

## Overview

The CMS package now uses a configuration-based approach for constants instead of hardcoded values. This allows end-developers to easily customize behavior by modifying the `cms_constants.php` config file.

## Configuration File

The constants are defined in `config/cms_constants.php` and organized into logical sections:

### Pagination Settings
```php
'pagination' => [
    'default_data_limit' => 20,
    'default_page' => 1,
],
```

### Image Directory Names
```php
'image_directories' => [
    'sector' => 'sector',
    'users' => 'users',
    'organizations' => 'organizations',
    'blog' => 'blog',
    'blog_attributes' => 'blogsAttributes',
    'blog_features' => 'blogsFeatures',
    'service' => 'service',
    'service_attributes' => 'servicesAttributes',
    'service_features' => 'servicesFeatures',
    'slider' => 'main-slider',
    'portfolio' => 'portfolio',
    'portfolio_attributes' => 'portfoliosAttributes',
    'portfolio_features' => 'portfoliosFeatures',
    'items_review' => 'items_reviews',
    'features' => 'features',
    'statistics' => 'statistics',
    'rewards' => 'rewads',
    'about_us' => 'aboutUs',
    'privacy_policy' => 'privacyPolicy',
    'term_of_service' => 'termOfService',
    'term_of_use' => 'termOfUse',
    'term_and_condition' => 'termAndCondition',
    'refund_policy' => 'refundPolicy',
    'our_mission' => 'ourMission',
    'our_story' => 'ourStory',
    'our_history' => 'ourHistory',
    'our_values' => 'ourValues',
    'our_vision' => 'ourVision',
    'brands' => 'brands',
    'defaults' => 'defaults',
    'testimonial' => 'testimonial',
    'teams' => 'teams',
],
```

### File Settings
```php
'files' => [
    'default_image_name' => 'def.png',
    'image_format' => 'webp',
    'portfolio_download_directory' => 'downloads/portfolios',
],
```

### Content Settings
```php
'content' => [
    'empty_html_content' => '<p></p>',
    'default_string' => '',
    'image_name' => '',
],
```

## Publishing the Config File

To customize the constants, you need to publish the config file to your application:

```bash
php artisan vendor:publish --tag=cms-config
```

This will copy `cms_constants.php` to your `config/` directory where you can modify the values.

## Usage Methods

### Method 1: Using Helper Functions (Recommended)

The package provides several helper functions for easy access:

```php
// Get pagination settings
$limit = cmsPagination('default_data_limit'); // Returns 20
$page = cmsPagination('default_page'); // Returns 1

// Get image directory names
$userDir = cmsImageDir('users'); // Returns 'users'
$blogDir = cmsImageDir('blog'); // Returns 'blog'

// Get file settings
$defaultImage = cmsFileSetting('default_image_name'); // Returns 'def.png'
$imageFormat = cmsFileSetting('image_format'); // Returns 'webp'

// Get content settings
$emptyContent = cmsContentSetting('empty_html_content'); // Returns '<p></p>'

// Generic config access
$value = cmsConfig('pagination.default_data_limit'); // Returns 20
$value = cmsConfig('image_directories.users'); // Returns 'users'
```

### Method 2: Using Laravel's Config Helper

```php
// Direct config access
$limit = config('cms_constants.pagination.default_data_limit');
$userDir = config('cms_constants.image_directories.users');
$defaultImage = config('cms_constants.files.default_image_name');
```

### Method 3: Using Constants (Backward Compatibility)

The old constants still work for backward compatibility:

```php
// These still work as before
$limit = DEFAULT_DATA_LIMIT;
$userDir = USERS_IMAGE_NAME;
$defaultImage = DEFAULT_IMAGE_NAME;
```

## Migration from Constants

If you're updating existing code, you can gradually migrate from constants to the new helper functions:

### Before (using constants):
```php
$folderPath = USERS_IMAGE_NAME;
$perPage = DEFAULT_DATA_LIMIT;
$imageFormat = IMAGE_FORMATE;
```

### After (using helper functions):
```php
$folderPath = cmsImageDir('users');
$perPage = cmsPagination('default_data_limit');
$imageFormat = cmsFileSetting('image_format');
```

## Benefits

1. **Easy Customization**: End-developers can modify values without touching package code
2. **Environment-Specific**: Different values for different environments
3. **Type Safety**: Better IDE support and type checking
4. **Documentation**: Clear structure and documentation of available options
5. **Backward Compatibility**: Existing code continues to work
6. **Performance**: Config values are cached in production

## Example Customization

To change the default pagination limit to 50 and user image directory to 'user-photos':

```php
// In config/cms_constants.php
return [
    'pagination' => [
        'default_data_limit' => 50, // Changed from 20
        'default_page' => 1,
    ],
    'image_directories' => [
        'users' => 'user-photos', // Changed from 'users'
        // ... other directories
    ],
    // ... rest of config
];
```

## Notes

- The config file is automatically loaded by the package
- Changes to the config file require clearing the config cache: `php artisan config:clear`
- The constants are still defined for backward compatibility, but they now read from the config
- Helper functions provide a more modern and flexible approach to accessing these values
