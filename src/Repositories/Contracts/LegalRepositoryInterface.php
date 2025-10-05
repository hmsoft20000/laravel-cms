<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\Legal\Legal;

interface LegalRepositoryInterface
{
    /**
     * Show model.
     *
     * @param Legal $model
     * @return Model
     */
    public function show(Legal $model): Legal;

    /**
     * Update model.
     *
     * @param Legal $model
     * @param array $data Data value
     * @return Model
     */
    public function update(Legal $model, array $data): Legal;
}
