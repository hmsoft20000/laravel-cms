<?php

namespace HMsoft\Cms\Services\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

class JoinManager
{
    private array $aliases = [];
    private Builder $query;
    private Model $mainModel;
    private string $mainTableAlias;

    public function __construct(Builder $query, string $mainTableAlias)
    {
        $this->query = $query;
        $this->mainModel = $query->getModel();
        $this->mainTableAlias = $mainTableAlias;
    }

    public function getMainTableAlias(): string
    {
        return $this->mainTableAlias;
    }

    /**
     * Ensures that all relationships in a given path are joined to the query.
     * It handles nested relationships by joining them sequentially.
     * Example: 'categories.sector.translations'
     *
     * @param string $relationPath The dot-notation path of relationships.
     * @return string The final alias for the last table in the path.
     * @throws \Exception
     */
    public function ensureJoin(string $relationPath): string
    {
        if (isset($this->aliases[$relationPath])) {
            return $this->aliases[$relationPath];
        }

        $currentModel = $this->mainModel;
        $parentAlias = $this->mainTableAlias;
        $pathSegments = explode('.', $relationPath);
        $currentPath = '';

        foreach ($pathSegments as $relationName) {
            $currentPath = $currentPath ? "{$currentPath}.{$relationName}" : $relationName;

            if (isset($this->aliases[$currentPath])) {
                $parentAlias = $this->aliases[$currentPath];
                $eloquentMethod = Str::camel($relationName);
                $currentModel = $currentModel->$eloquentMethod()->getRelated();
                continue;
            }

            $alias = 't_' . Str::snake(str_replace('.', '_', $currentPath));

            $eloquentMethod = Str::camel($relationName);
            if (!method_exists($currentModel, $eloquentMethod)) {
                throw new \Exception("Relation '{$eloquentMethod}' does not exist on model " . get_class($currentModel));
            }

            /** @var Relation $relationObject */
            $relationObject = $currentModel->$eloquentMethod();
            $relatedTable = $relationObject->getRelated()->getTable();

            $this->performJoin($relationObject, $relatedTable, $alias, $parentAlias);

            $currentModel = $relationObject->getRelated();
            $parentAlias = $alias;
            $this->aliases[$currentPath] = $alias;
        }

        return $parentAlias;
    }

    /**
     * Performs the appropriate database join based on the Eloquent relation type.
     */
    private function performJoin(Relation $relation, string $relatedTable, string $alias, string $parentAlias): void
    {
        if ($relation instanceof HasOne || $relation instanceof HasMany) {
            $this->query->leftJoin(
                "{$relatedTable} as {$alias}",
                "{$parentAlias}." . $relation->getLocalKeyName(),
                '=',
                "{$alias}." . $relation->getForeignKeyName()
            );
        } elseif ($relation instanceof BelongsTo) {
            $this->query->leftJoin(
                "{$parentAlias}." . $relation->getForeignKeyName(),
                '=',
                "{$alias}." . $relation->getOwnerKeyName()
            );
        } elseif ($relation instanceof BelongsToMany) {
            $pivotTable = $relation->getTable();
            $pivotAlias = 'pivot_' . Str::snake(str_replace('.', '_', $alias));

            $this->query->leftJoin(
                "{$pivotTable} as {$pivotAlias}",
                function ($join) use ($relation, $parentAlias, $pivotAlias) {
                    $join->on(
                        "{$parentAlias}." . $relation->getParentKeyName(),
                        '=',
                        "{$pivotAlias}." . $relation->getForeignPivotKeyName()
                    );

                    if ($relation instanceof MorphToMany) {
                        $join->where("{$pivotAlias}." . $relation->getMorphType(), $relation->getMorphClass());
                    }
                }
            );

            $this->query->leftJoin(
                "{$relatedTable} as {$alias}",
                "{$pivotAlias}." . $relation->getRelatedPivotKeyName(),
                '=',
                "{$alias}." . $relation->getRelatedKeyName()
            );
        } else {
            throw new \Exception("Unsupported relationship type for JoinManager: " . get_class($relation));
        }
    }
}
