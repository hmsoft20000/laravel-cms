<?php

namespace HMsoft\Cms\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;


interface BaseRepositoryInterface
{

    /**
     * Create model.
     *
     * @param array $data Data value
     * @return Model
     */
    public function store(array $data): Model;

    /**
     * Show model.
     *
     * @param Model $model
     * @return Model
     */
    public function show(Model $model): Model;

    /**
     * Update model.
     *
     * @param Model $model
     * @param array $data Data value
     * @return Model
     */
    public function update(Model $model, array $data): Model;

    /**
     * Delete model.
     *
     * @param Model $model
     * @return bool
     */
    public function delete(Model $model): bool;

    /**
     * Destroy model (force delete/hard delete).
     *
     * @param Model $model
     * @return bool
     */
    public function destroy(Model $model): bool;
}
