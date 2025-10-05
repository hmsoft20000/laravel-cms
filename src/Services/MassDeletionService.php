<?php

namespace HMsoft\Cms\Services;

use Illuminate\Database\Eloquent\Builder;

class MassDeletionService
{
    private Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Executes the mass deletion with a pre-deletion check.
     *
     * @param callable $deletableCondition A closure that returns true if the model can be deleted.
     * @return array
     */
    public function deleteWithCondition(callable $deletableCondition, callable $deleteAction): array
    {
        $records = $this->query->get();
        $deletedCount = 0;
        $notDeleted = collect();

        foreach ($records as $record) {
            if ($deletableCondition($record)) {
                $deleteAction($record);
                $deletedCount++;
            } else {
                $notDeleted->push($record);
            }
        }

        return [
            'deleted_count' => $deletedCount,
            'not_deleted_records' => $notDeleted,
        ];
    }
}
