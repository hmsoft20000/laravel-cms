<?php

namespace HMsoft\Cms\Services\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class JoinManager
{
    private array $aliases = [];
    private Builder $query;
    private string $mainTableAlias;

    public function __construct(Builder $query, string $mainTableAlias)
    {
        $this->query = $query;
        $this->mainTableAlias = $mainTableAlias;
    }

    public function ensureJoin(string $relationName): string
    {
        if (isset($this->aliases[$relationName])) {
            return $this->aliases[$relationName];
        }

        $model = $this->query->getModel();
        $relationMethod = Str::camel($relationName);

        if (!method_exists($model, $relationMethod)) {
            throw new \Exception("Relation '{$relationName}' does not exist on model " . get_class($model));
        }

        /** @var Relation $relationObject */
        $relationObject = $model->$relationMethod();
        $relatedTable = $relationObject->getRelated()->getTable();
        $alias = 't_' . Str::snake($relationName);

        // ## LOGIC FOR ALL RELATIONSHIP TYPES ##

        if ($relationObject instanceof HasOne || $relationObject instanceof HasMany) {
            // Handles both HasOne and HasMany relationships.
            // Example: A Post `hasOne` FeaturedImage or `hasMany` Comments.
            // JOIN `comments` ON `t_main`.`id` = `t_comments`.`post_id`
            $this->query->leftJoin(
                "{$relatedTable} as {$alias}",
                "{$this->mainTableAlias}." . $relationObject->getLocalKeyName(),
                '=',
                "{$alias}." . $relationObject->getForeignKeyName()
            );
        } elseif ($relationObject instanceof BelongsTo) {
            // Handles BelongsTo relationships.
            // Example: A Post `belongsTo` an Author.
            // JOIN `users` ON `t_main`.`user_id` = `t_author`.`id`
            $this->query->leftJoin(
                "{$relatedTable} as {$alias}",
                "{$this->mainTableAlias}." . $relationObject->getForeignKeyName(),
                '=',
                "{$alias}." . $relationObject->getOwnerKeyName()
            );
        } elseif ($relationObject instanceof BelongsToMany) {
            // Handles BelongsToMany relationships.
            // This requires joining the intermediate "pivot" table first.
            // Example: A Post `belongsToMany` Tags, via the `post_tag` pivot table.

            $pivotTable = $relationObject->getTable(); // e.g., 'post_tag'
            $pivotAlias = 'pivot_' . Str::snake($relationName); // e.g., 'pivot_tags'

            // 1. Join the PIVOT table to the MAIN table
            $this->query->leftJoin(
                "{$pivotTable} as {$pivotAlias}",
                "{$this->mainTableAlias}." . $relationObject->getParentKeyName(),
                '=',
                "{$pivotAlias}." . $relationObject->getForeignPivotKeyName()
            );

            // 2. Join the RELATED table to the PIVOT table
            $this->query->leftJoin(
                "{$relatedTable} as {$alias}",
                "{$pivotAlias}." . $relationObject->getRelatedPivotKeyName(),
                '=',
                "{$alias}." . $relationObject->getRelatedKeyName()
            );
        } else {
            throw new \Exception("Unsupported relationship type for JoinManager: " . get_class($relationObject));
        }

        return $this->aliases[$relationName] = $alias;
    }

    public function getMainTableAlias(): string
    {
        return $this->mainTableAlias;
    }
}
