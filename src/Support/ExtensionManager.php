<?php

namespace HMsoft\Cms\Support;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class ExtensionManager
{
    protected static array $types = [
        'models' => [
            'config_key' => 'cms.extended_models',
            'allow_key'  => 'cms.allow_full_model_replacement',
        ],
        'controllers' => [
            'config_key' => 'cms.extended_controllers',
            'allow_key'  => 'cms.allow_full_controller_replacement',
        ],
        'resources' => [
            'config_key' => 'cms.extended_resources',
            'allow_key'  => 'cms.allow_full_resource_replacement',
        ],
        'form_requests' => [
            'config_key' => 'cms.extended_form_requests',
            'allow_key'  => 'cms.allow_full_form_request_replacement',
        ],
        'repositories' => [
            'config_key' => 'cms.extended_repositories',
            'allow_key'  => 'cms.allow_full_repository_replacement',
        ],
    ];

    protected static array $extended = [];

    /**
     * Apply all extension types.
     */
    public static function applyAll(): void
    {
        foreach (array_keys(static::$types) as $type) {
            static::apply($type);
        }
    }

    /**
     * Apply a specific extension type.
     */
    public static function apply(string $type): void
    {
        if (!isset(static::$types[$type])) {
            throw new InvalidArgumentException("Unknown extension type: {$type}");
        }

        $configKey = static::$types[$type]['config_key'];
        $allowKey  = static::$types[$type]['allow_key'];

        $mappings = config($configKey, []);
        $allowReplacement = config($allowKey, false);

        foreach ($mappings as $original => $extended) {
            if (!class_exists($original) || !class_exists($extended)) {
                continue;
            }

            // Check inheritance
            if (!is_subclass_of($extended, $original)) {
                if (!$allowReplacement) {
                    throw new InvalidArgumentException(
                        "The extended {$type} [{$extended}] must extend [{$original}]. "
                            . "To allow full replacement, set '{$allowKey}' => true."
                    );
                }

                Log::warning("{$type} [{$extended}] replaces [{$original}] "
                    . "without extending it. Ensure compatibility manually.");
            }

            // Register binding and alias
            static::$extended[$original] = $extended;
            App::bind($original, $extended);
            // class_alias($extended, $original);
        }
    }
}
