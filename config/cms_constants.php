<?php

/**
 * =================================================================
 * CMS Constants Configuration
 * =================================================================
 * 
 * This file contains all the configurable constants for the CMS package.
 * End-developers can modify these values to customize the behavior
 * of the CMS without modifying the package code.
 */

return [
    /**
     * =================================================================
     * Pagination Settings
     * =================================================================
     */
    'pagination' => [
        'default_data_limit' => 20,
        'default_page' => 1,
    ],

    /**
     * =================================================================
     * Image Directory Names
     * =================================================================
     * These define the folder names where different types of images
     * are stored in the public storage directory.
     */
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

    /**
     * =================================================================
     * File Settings
     * =================================================================
     */
    'files' => [
        'default_image_name' => 'def.png',
        'image_format' => 'webp',
        'portfolio_download_directory' => 'downloads/portfolios',
    ],

    /**
     * =================================================================
     * Content Settings
     * =================================================================
     */
    'content' => [
        'empty_html_content' => '<p></p>',
        'default_string' => '',
        'image_name' => '',
    ],
];
