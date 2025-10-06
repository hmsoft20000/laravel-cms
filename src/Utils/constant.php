<?php

/**
 * =================================================================
 * CMS Constants Helper
 * =================================================================
 * 
 * This file provides backward compatibility by defining constants
 * from the config file. It allows existing code to continue working
 * while enabling end-developers to customize values through config.
 */

// Load config values and define constants for backward compatibility
$constants = config('cms_constants', []);

// Pagination constants
if (!defined('DEFAULT_DATA_LIMIT')) {
    define('DEFAULT_DATA_LIMIT', $constants['pagination']['default_data_limit'] ?? 20);
}
if (!defined('DEFAULT_PAGE')) {
    define('DEFAULT_PAGE', $constants['pagination']['default_page'] ?? 1);
}

// Image directory constants
if (!defined('SECTOR_IMAGE_NAME')) {
    define('SECTOR_IMAGE_NAME', $constants['image_directories']['sector'] ?? 'sector');
}
if (!defined('USERS_IMAGE_NAME')) {
    define('USERS_IMAGE_NAME', $constants['image_directories']['users'] ?? 'users');
}
if (!defined('ORGANIZATION_IMAGE_NAME')) {
    define('ORGANIZATION_IMAGE_NAME', $constants['image_directories']['organizations'] ?? 'organizations');
}
if (!defined('BLOG_IMAGE_NAME')) {
    define('BLOG_IMAGE_NAME', $constants['image_directories']['blog'] ?? 'blog');
}
if (!defined('BLOG_ATTRIBUTE_IMAGE_NAME')) {
    define('BLOG_ATTRIBUTE_IMAGE_NAME', $constants['image_directories']['blog_attributes'] ?? 'blogsAttributes');
}
if (!defined('BLOG_FEATURE_IMAGE_NAME')) {
    define('BLOG_FEATURE_IMAGE_NAME', $constants['image_directories']['blog_features'] ?? 'blogsFeatures');
}
if (!defined('SERVICE_IMAGE_NAME')) {
    define('SERVICE_IMAGE_NAME', $constants['image_directories']['service'] ?? 'service');
}
if (!defined('SERVICE_ATTRIBUTE_IMAGE_NAME')) {
    define('SERVICE_ATTRIBUTE_IMAGE_NAME', $constants['image_directories']['service_attributes'] ?? 'servicesAttributes');
}
if (!defined('SERVICE_FEATURE_IMAGE_NAME')) {
    define('SERVICE_FEATURE_IMAGE_NAME', $constants['image_directories']['service_features'] ?? 'servicesFeatures');
}
if (!defined('SLIDER_IMAGE_NAME')) {
    define('SLIDER_IMAGE_NAME', $constants['image_directories']['slider'] ?? 'main-slider');
}
if (!defined('PORTFOLIO_IMAGE_NAME')) {
    define('PORTFOLIO_IMAGE_NAME', $constants['image_directories']['portfolio'] ?? 'portfolio');
}
if (!defined('PORTFOLIO_ATTRIBUTE_IMAGE_NAME')) {
    define('PORTFOLIO_ATTRIBUTE_IMAGE_NAME', $constants['image_directories']['portfolio_attributes'] ?? 'portfoliosAttributes');
}
if (!defined('PORTFOLIO_FEATURE_IMAGE_NAME')) {
    define('PORTFOLIO_FEATURE_IMAGE_NAME', $constants['image_directories']['portfolio_features'] ?? 'portfoliosFeatures');
}
if (!defined('ITEMS_REVIEW_IMAGE_NAME')) {
    define('ITEMS_REVIEW_IMAGE_NAME', $constants['image_directories']['items_review'] ?? 'items_reviews');
}
if (!defined('FEATURE_IMAGE_NAME')) {
    define('FEATURE_IMAGE_NAME', $constants['image_directories']['features'] ?? 'features');
}
if (!defined('STATISTICS_IMAGE_NAME')) {
    define('STATISTICS_IMAGE_NAME', $constants['image_directories']['statistics'] ?? 'statistics');
}
if (!defined('REWADS_IMAGE_NAME')) {
    define('REWADS_IMAGE_NAME', $constants['image_directories']['rewards'] ?? 'rewads');
}
if (!defined('ABOUT_IMAGE_NAME')) {
    define('ABOUT_IMAGE_NAME', $constants['image_directories']['about_us'] ?? 'aboutUs');
}
if (!defined('PRIVACY_POLICY_IMAGE_NAME')) {
    define('PRIVACY_POLICY_IMAGE_NAME', $constants['image_directories']['privacy_policy'] ?? 'privacyPolicy');
}
if (!defined('TERM_OF_SERVICE_IMAGE_NAME')) {
    define('TERM_OF_SERVICE_IMAGE_NAME', $constants['image_directories']['term_of_service'] ?? 'termOfService');
}
if (!defined('TERM_OF_USE_IMAGE_NAME')) {
    define('TERM_OF_USE_IMAGE_NAME', $constants['image_directories']['term_of_use'] ?? 'termOfUse');
}
if (!defined('TERM_AND_CONDITION_IMAGE_NAME')) {
    define('TERM_AND_CONDITION_IMAGE_NAME', $constants['image_directories']['term_and_condition'] ?? 'termAndCondition');
}
if (!defined('REFUND_POLICY_IMAGE_NAME')) {
    define('REFUND_POLICY_IMAGE_NAME', $constants['image_directories']['refund_policy'] ?? 'refundPolicy');
}
if (!defined('OUR_MISSION_IMAGE_NAME')) {
    define('OUR_MISSION_IMAGE_NAME', $constants['image_directories']['our_mission'] ?? 'ourMission');
}
if (!defined('OUR_STORY_IMAGE_NAME')) {
    define('OUR_STORY_IMAGE_NAME', $constants['image_directories']['our_story'] ?? 'ourStory');
}
if (!defined('OUR_HISTORY_IMAGE_NAME')) {
    define('OUR_HISTORY_IMAGE_NAME', $constants['image_directories']['our_history'] ?? 'ourHistory');
}
if (!defined('OUR_VALUES_IMAGE_NAME')) {
    define('OUR_VALUES_IMAGE_NAME', $constants['image_directories']['our_values'] ?? 'ourValues');
}
if (!defined('OUR_VISION_IMAGE_NAME')) {
    define('OUR_VISION_IMAGE_NAME', $constants['image_directories']['our_vision'] ?? 'ourVision');
}
if (!defined('BRAND_IMAGE_NAME')) {
    define('BRAND_IMAGE_NAME', $constants['image_directories']['brands'] ?? 'brands');
}
if (!defined('DEFAULTS_IMAGE_NAME')) {
    define('DEFAULTS_IMAGE_NAME', $constants['image_directories']['defaults'] ?? 'defaults');
}
if (!defined('TESTIMONIAL_IMAGE')) {
    define('TESTIMONIAL_IMAGE', $constants['image_directories']['testimonial'] ?? 'testimonial');
}
if (!defined('TEAMS_IMAGE_NAME')) {
    define('TEAMS_IMAGE_NAME', $constants['image_directories']['teams'] ?? 'teams');
}

// File constants
if (!defined('DEFAULT_IMAGE_NAME')) {
    define('DEFAULT_IMAGE_NAME', $constants['files']['default_image_name'] ?? 'def.png');
}
if (!defined('IMAGE_FORMATE')) {
    define('IMAGE_FORMATE', $constants['files']['image_format'] ?? 'webp');
}
if (!defined('PORTFOLIO_DOWNLOAD_FILE_NAME')) {
    define('PORTFOLIO_DOWNLOAD_FILE_NAME', $constants['files']['portfolio_download_directory'] ?? 'downloads/portfolios');
}

// Content constants
if (!defined('EMPTY_HTML_CONTENT')) {
    define('EMPTY_HTML_CONTENT', $constants['content']['empty_html_content'] ?? '<p></p>');
}
if (!defined('DEFAULT_STRING')) {
    define('DEFAULT_STRING', $constants['content']['default_string'] ?? '');
}
if (!defined('IMAGE_NAME')) {
    define('IMAGE_NAME', $constants['content']['image_name'] ?? '');
}
