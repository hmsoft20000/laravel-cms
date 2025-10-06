<?php

namespace HMsoft\Cms\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddCustomRelationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:add-relation 
                            {model : The model class name (e.g., Category)}
                            {relation : The relationship name (e.g., properties)}
                            {type : The relationship type (hasMany, belongsTo, hasOne, belongsToMany, morphMany, morphTo, morphOne)}
                            {related : The related model class name (e.g., App\\Models\\Property)}
                            {--foreign-key= : The foreign key column name}
                            {--local-key=id : The local key column name}
                            {--owner-key=id : The owner key column name (for belongsTo)}
                            {--table= : The pivot table name (for belongsToMany)}
                            {--related-foreign-key= : The related foreign key (for belongsToMany)}
                            {--morph-name= : The morph name (for morph relationships)}
                            {--morph-type= : The morph type column (for morph relationships)}
                            {--morph-id= : The morph id column (for morph relationships)}
                            {--pivot-columns= : Comma-separated pivot columns (for belongsToMany)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a custom relationship to a CMS model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->argument('model');
        $relation = $this->argument('relation');
        $type = $this->argument('type');
        $related = $this->argument('related');

        // Build the relationship configuration
        $config = [
            'type' => $type,
            'related' => $related,
        ];

        // Add type-specific options
        switch ($type) {
            case 'hasMany':
            case 'hasOne':
            case 'belongsTo':
                $config['foreign_key'] = $this->option('foreign-key') ?: $this->getDefaultForeignKey($model, $relation);
                $config['local_key'] = $this->option('local-key');
                if ($type === 'belongsTo') {
                    $config['owner_key'] = $this->option('owner-key');
                }
                break;

            case 'belongsToMany':
                $config['table'] = $this->option('table') ?: $this->getDefaultPivotTable($model, $relation);
                $config['foreign_key'] = $this->option('foreign-key') ?: $this->getDefaultForeignKey($model, $relation);
                $config['related_foreign_key'] = $this->option('related-foreign-key') ?: $this->getDefaultRelatedForeignKey($related);
                $config['local_key'] = $this->option('local-key');
                $config['owner_key'] = $this->option('owner-key');
                if ($this->option('pivot-columns')) {
                    $config['pivot_columns'] = explode(',', $this->option('pivot-columns'));
                }
                break;

            case 'morphMany':
            case 'morphOne':
            case 'morphTo':
                $config['morph_name'] = $this->option('morph-name') ?: 'morphable';
                $config['morph_type'] = $this->option('morph-type') ?: $config['morph_name'] . '_type';
                $config['morph_id'] = $this->option('morph-id') ?: $config['morph_name'] . '_id';
                break;
        }

        // Get the full model class name
        $modelClass = $this->getModelClass($model);

        // Add the relationship using the helper function
        addCustomRelation($modelClass, $relation, $config);

        // Update the config file
        $this->updateConfigFile($modelClass, $relation, $config);

        $this->info("Custom relationship '{$relation}' added to {$modelClass} successfully!");
        $this->line("You can now use: \${$model}->{$relation}");
    }

    /**
     * Get the full model class name
     */
    private function getModelClass(string $model): string
    {
        // Try common CMS model namespaces
        $namespaces = [
            'HMsoft\\Cms\\Models\\Shared\\',
            'HMsoft\\Cms\\Models\\Content\\',
            'HMsoft\\Cms\\Models\\Sector\\',
            'HMsoft\\Cms\\Models\\Organizations\\',
            'HMsoft\\Cms\\Models\\Team\\',
            'HMsoft\\Cms\\Models\\Statistics\\',
        ];

        foreach ($namespaces as $namespace) {
            $fullClass = $namespace . $model;
            if (class_exists($fullClass)) {
                return $fullClass;
            }
        }

        // If not found, assume it's a full class name
        return $model;
    }

    /**
     * Get default foreign key name
     */
    private function getDefaultForeignKey(string $model, string $relation): string
    {
        return strtolower($model) . '_id';
    }

    /**
     * Get default related foreign key name
     */
    private function getDefaultRelatedForeignKey(string $related): string
    {
        $className = class_basename($related);
        return strtolower($className) . '_id';
    }

    /**
     * Get default pivot table name
     */
    private function getDefaultPivotTable(string $model, string $relation): string
    {
        $modelName = strtolower($model);
        $relationName = strtolower($relation);
        return $modelName . '_' . $relationName;
    }

    /**
     * Update the config file
     */
    private function updateConfigFile(string $modelClass, string $relation, array $config): void
    {
        $configPath = config_path('cms_custom_relations.php');
        
        if (!File::exists($configPath)) {
            $this->error('Config file not found. Please run: php artisan vendor:publish --tag=cms-config');
            return;
        }

        $content = File::get($configPath);
        
        // This is a simplified approach - in a real implementation,
        // you might want to use a more sophisticated method to update the PHP array
        $this->warn('Please manually add the relationship to config/cms_custom_relations.php:');
        $this->line("'{$relation}' => " . var_export($config, true) . ',');
    }
}
