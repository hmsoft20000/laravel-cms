<?php

namespace HMsoft\Cms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeExtendedModelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:make-extended-model 
                            {model : The CMS model name (e.g., Category, Post, Sector)}
                            {--name= : The extended model name (defaults to the same name)}
                            {--namespace=App\\Models : The namespace for the extended model}
                            {--force : Overwrite existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an extended model that extends a CMS model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modelName = $this->argument('model');
        $extendedName = $this->option('name') ?: $modelName;
        $namespace = $this->option('namespace');
        $force = $this->option('force');

        // Get the original model class
        $originalClass = $this->getOriginalModelClass($modelName);
        
        if (!$originalClass) {
            $this->error("CMS model '{$modelName}' not found!");
            return 1;
        }

        // Get the extended model class
        $extendedClass = $namespace . '\\' . $extendedName;
        $extendedPath = $this->getModelPath($extendedClass);

        // Check if file already exists
        if (File::exists($extendedPath) && !$force) {
            $this->error("Model already exists at: {$extendedPath}");
            $this->info("Use --force to overwrite.");
            return 1;
        }

        // Create the directory if it doesn't exist
        $directory = dirname($extendedPath);
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        // Generate the model content
        $content = $this->generateModelContent($originalClass, $extendedClass, $extendedName);

        // Write the file
        File::put($extendedPath, $content);

        // Update the config file
        $this->updateConfigFile($originalClass, $extendedClass);

        $this->info("Extended model created successfully!");
        $this->line("File: {$extendedPath}");
        $this->line("Class: {$extendedClass}");
        $this->line("Extends: {$originalClass}");
        
        $this->newLine();
        $this->info("You can now add custom relationships, methods, and properties to your extended model!");
        $this->line("Example: Add a 'properties' relationship to Category model");

        return 0;
    }

    /**
     * Get the original model class
     */
    private function getOriginalModelClass(string $modelName): ?string
    {
        $namespaces = [
            'HMsoft\\Cms\\Models\\Shared\\',
            'HMsoft\\Cms\\Models\\Content\\',
            'HMsoft\\Cms\\Models\\Sector\\',
            'HMsoft\\Cms\\Models\\Organizations\\',
            'HMsoft\\Cms\\Models\\Team\\',
            'HMsoft\\Cms\\Models\\Statistics\\',
        ];

        foreach ($namespaces as $namespace) {
            $fullClass = $namespace . $modelName;
            if (class_exists($fullClass)) {
                return $fullClass;
            }
        }

        return null;
    }

    /**
     * Get the model file path
     */
    private function getModelPath(string $class): string
    {
        $namespace = substr($class, 0, strrpos($class, '\\'));
        $className = substr($class, strrpos($class, '\\') + 1);
        
        $path = str_replace('\\', '/', $namespace);
        $path = app_path($path);
        
        return $path . '/' . $className . '.php';
    }

    /**
     * Generate the model content
     */
    private function generateModelContent(string $originalClass, string $extendedClass, string $className): string
    {
        $originalNamespace = substr($originalClass, 0, strrpos($originalClass, '\\'));
        $originalName = substr($originalClass, strrpos($originalClass, '\\') + 1);
        
        $extendedNamespace = substr($extendedClass, 0, strrpos($extendedClass, '\\'));
        $extendedName = substr($extendedClass, strrpos($extendedClass, '\\') + 1);

        return "<?php

namespace {$extendedNamespace};

use {$originalClass};

/**
 * Extended {$originalName} Model
 * 
 * This model extends the CMS {$originalName} model and allows you to add
 * custom relationships, methods, scopes, and properties.
 * 
 * You can add any custom functionality here without modifying the original CMS model.
 */
class {$extendedName} extends {$originalName}
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected \$table = '{$this->getTableName($originalClass)}';

    // =================================================================
    // CUSTOM RELATIONSHIPS
    // =================================================================
    
    /**
     * Example: Add a custom relationship
     * Uncomment and modify as needed
     */
    /*
    public function properties()
    {
        return \$this->hasMany(Property::class, 'category_id');
    }
    
    public function tags()
    {
        return \$this->belongsToMany(Tag::class, 'category_tag', 'category_id', 'tag_id');
    }
    */

    // =================================================================
    // CUSTOM METHODS
    // =================================================================
    
    /**
     * Example: Add custom methods
     * Uncomment and modify as needed
     */
    /*
    public function getCustomAttribute()
    {
        return 'Custom value for ' . \$this->name;
    }
    
    public function customMethod()
    {
        // Your custom logic here
        return \$this->where('is_active', true);
    }
    */

    // =================================================================
    // CUSTOM SCOPES
    // =================================================================
    
    /**
     * Example: Add custom scopes
     * Uncomment and modify as needed
     */
    /*
    public function scopeCustom(\$query)
    {
        return \$query->where('custom_field', 'value');
    }
    */

    // =================================================================
    // CUSTOM ACCESSORS & MUTATORS
    // =================================================================
    
    /**
     * Example: Add custom accessors and mutators
     * Uncomment and modify as needed
     */
    /*
    protected function customField(): Attribute
    {
        return Attribute::make(
            get: fn (\$value) => strtoupper(\$value),
            set: fn (\$value) => strtolower(\$value),
        );
    }
    */
}";
    }

    /**
     * Get the table name from the original model
     */
    private function getTableName(string $originalClass): string
    {
        try {
            $model = new $originalClass();
            return $model->getTable();
        } catch (\Exception $e) {
            return 'unknown_table';
        }
    }

    /**
     * Update the config file
     */
    private function updateConfigFile(string $originalClass, string $extendedClass): void
    {
        $configPath = config_path('cms_extended_models.php');
        
        if (!File::exists($configPath)) {
            $this->warn('Config file not found. Please run: php artisan vendor:publish --tag=cms-config');
            return;
        }

        $this->info("Please add the following to your config/cms_extended_models.php:");
        $this->line("'{$originalClass}' => '{$extendedClass}',");
    }
}
