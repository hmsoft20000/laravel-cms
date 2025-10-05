<?php

/**
 * Route Configuration Examples
 * 
 * This file contains practical examples of how to configure routes
 * in the HMsoft Laravel CMS package.
 */

return [
    
    // Example 1: Basic Configuration
    'basic_example' => [
        'enabled' => [
            'posts' => true,
            'auth' => true,
            'settings' => false, // Disable settings
        ],
        'global_middleware' => [
            'set.web_config',
            'set.language',
            'optional.sanctum'
        ],
    ],

    // Example 2: Custom Authentication Routes
    'custom_auth_example' => [
        'groups' => [
            'auth' => [
                'enabled' => true,
                'prefix' => 'auth',
                'middleware' => ['throttle:10,1'],
                'as' => 'auth.',
                'routes' => [
                    'login' => [
                        'method' => 'post',
                        'uri' => 'login',
                        'action' => 'AuthController@login',
                        'middleware' => ['throttle:5,1'],
                    ],
                    'register' => [
                        'method' => 'post',
                        'uri' => 'register',
                        'action' => 'CustomAuthController@register',
                        'middleware' => ['throttle:3,1'],
                    ],
                    'social_login' => [
                        'method' => 'post',
                        'uri' => 'social/{provider}',
                        'action' => 'SocialAuthController@login',
                        'middleware' => [],
                    ],
                ],
            ],
        ],
    ],

    // Example 3: E-commerce Routes
    'ecommerce_example' => [
        'custom' => [
            'products' => [
                'method' => 'get',
                'uri' => 'products',
                'action' => 'ProductController@index',
                'middleware' => ['auth:sanctum'],
                'as' => 'api.',
            ],
            'cart' => [
                'method' => 'get',
                'uri' => 'cart',
                'action' => 'CartController@index',
                'middleware' => ['auth:sanctum'],
                'as' => 'api.',
            ],
            'checkout' => [
                'method' => 'post',
                'uri' => 'checkout',
                'action' => 'CheckoutController@process',
                'middleware' => ['auth:sanctum', 'throttle:5,1'],
                'as' => 'api.',
            ],
        ],
    ],

    // Example 4: API Versioning
    'api_versioning_example' => [
        'groups' => [
            'posts' => [
                'enabled' => true,
                'prefix' => 'v1/posts',
                'middleware' => ['api.version:v1'],
                'as' => 'api.v1.',
            ],
        ],
        'custom' => [
            'v2_posts' => [
                'method' => 'get',
                'uri' => 'v2/posts',
                'action' => 'V2\PostController@index',
                'middleware' => ['api.version:v2'],
                'as' => 'api.v2.',
            ],
        ],
    ],

    // Example 5: Multi-tenant Routes
    'multitenant_example' => [
        'groups' => [
            'posts' => [
                'enabled' => true,
                'prefix' => '{tenant}/posts',
                'middleware' => ['tenant.required'],
                'as' => 'api.',
            ],
        ],
        'custom' => [
            'tenant_switch' => [
                'method' => 'post',
                'uri' => 'tenant/switch',
                'action' => 'TenantController@switch',
                'middleware' => ['auth:sanctum'],
                'as' => 'api.',
            ],
        ],
    ],

    // Example 6: Admin Panel Routes
    'admin_panel_example' => [
        'groups' => [
            'authorization' => [
                'enabled' => true,
                'prefix' => 'admin',
                'middleware' => ['auth:sanctum', 'admin.required'],
                'as' => 'admin.',
            ],
        ],
        'custom' => [
            'admin_dashboard' => [
                'method' => 'get',
                'uri' => 'admin/dashboard',
                'action' => 'Admin\DashboardController@index',
                'middleware' => ['auth:sanctum', 'admin.required'],
                'as' => 'admin.',
            ],
            'admin_analytics' => [
                'method' => 'get',
                'uri' => 'admin/analytics',
                'action' => 'Admin\AnalyticsController@index',
                'middleware' => ['auth:sanctum', 'admin.required', 'throttle:10,1'],
                'as' => 'admin.',
            ],
        ],
    ],

    // Example 7: Mobile API Routes
    'mobile_api_example' => [
        'groups' => [
            'auth' => [
                'enabled' => true,
                'prefix' => 'mobile/auth',
                'middleware' => ['mobile.api'],
                'as' => 'mobile.',
                'routes' => [
                    'login' => [
                        'method' => 'post',
                        'uri' => 'login',
                        'action' => 'Mobile\AuthController@login',
                        'middleware' => ['throttle:10,1'],
                    ],
                    'refresh' => [
                        'method' => 'post',
                        'uri' => 'refresh',
                        'action' => 'Mobile\AuthController@refresh',
                        'middleware' => ['auth:sanctum'],
                    ],
                ],
            ],
        ],
    ],

    // Example 8: Webhook Routes
    'webhook_example' => [
        'custom' => [
            'stripe_webhook' => [
                'method' => 'post',
                'uri' => 'webhooks/stripe',
                'action' => 'WebhookController@stripe',
                'middleware' => ['webhook.verify:stripe'],
                'as' => 'webhooks.',
            ],
            'paypal_webhook' => [
                'method' => 'post',
                'uri' => 'webhooks/paypal',
                'action' => 'WebhookController@paypal',
                'middleware' => ['webhook.verify:paypal'],
                'as' => 'webhooks.',
            ],
        ],
    ],

    // Example 9: File Upload Routes
    'file_upload_example' => [
        'custom' => [
            'upload_image' => [
                'method' => 'post',
                'uri' => 'upload/image',
                'action' => 'UploadController@image',
                'middleware' => ['auth:sanctum', 'throttle:20,1'],
                'as' => 'api.',
            ],
            'upload_document' => [
                'method' => 'post',
                'uri' => 'upload/document',
                'action' => 'UploadController@document',
                'middleware' => ['auth:sanctum', 'throttle:10,1'],
                'as' => 'api.',
            ],
        ],
    ],

    // Example 10: Complete Custom Configuration
    'complete_example' => [
        'enabled' => [
            'posts' => true,
            'organizations' => true,
            'legals' => true,
            'auth' => true,
            'settings' => true,
            'content_management' => true,
            'authorization' => false, // Disable authorization
            'public' => true,
        ],
        'global_middleware' => [
            'set.web_config',
            'set.language',
            'optional.sanctum',
            'cors',
        ],
        'groups' => [
            'posts' => [
                'enabled' => true,
                'middleware' => ['throttle:60,1'],
                'sub_routes' => [
                    'media' => [
                        'enabled' => true,
                        'middleware' => ['auth:sanctum'],
                    ],
                    'categories' => [
                        'enabled' => true,
                        'middleware' => ['auth:sanctum'],
                    ],
                ],
            ],
        ],
        'custom' => [
            'health_check' => [
                'method' => 'get',
                'uri' => 'health',
                'closure' => function() {
                    return response()->json(['status' => 'ok', 'timestamp' => now()]);
                },
                'middleware' => [],
                'as' => 'api.',
            ],
            'api_docs' => [
                'method' => 'get',
                'uri' => 'docs',
                'action' => 'DocumentationController@index',
                'middleware' => ['auth:sanctum'],
                'as' => 'api.',
            ],
        ],
        'overrides' => [
            'auth.login' => [
                'method' => 'post',
                'uri' => 'login',
                'action' => 'CustomAuthController@login',
                'middleware' => ['throttle:5,1', 'custom.validation'],
                'as' => 'auth.',
            ],
        ],
        'exclude' => [
            'auth.register',
            'permissions.bulk_assign_role',
        ],
    ],

    // Example 11: Development vs Production
    'environment_example' => [
        'enabled' => [
            'posts' => true,
            'auth' => true,
            'settings' => true,
            'content_management' => true,
            'authorization' => env('APP_ENV') === 'production', // Only in production
            'public' => true,
        ],
        'custom' => array_merge(
            [
                'api_status' => [
                    'method' => 'get',
                    'uri' => 'status',
                    'closure' => function() {
                        return response()->json(['status' => 'ok']);
                    },
                    'middleware' => [],
                    'as' => 'api.',
                ],
            ],
            env('APP_ENV') === 'local' ? [
                'debug_routes' => [
                    'method' => 'get',
                    'uri' => 'debug/routes',
                    'action' => 'DebugController@routes',
                    'middleware' => ['auth:sanctum'],
                    'as' => 'api.',
                ],
            ] : []
        ),
    ],

    // Example 12: Multi-language Routes
    'multilang_example' => [
        'groups' => [
            'posts' => [
                'enabled' => true,
                'prefix' => '{locale}/posts',
                'middleware' => ['set.locale'],
                'as' => 'api.',
            ],
        ],
        'custom' => [
            'locale_switch' => [
                'method' => 'post',
                'uri' => 'locale/switch',
                'action' => 'LocaleController@switch',
                'middleware' => ['auth:sanctum'],
                'as' => 'api.',
            ],
        ],
    ],
];
