<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shared\Faq;
use HMsoft\Cms\Repositories\Contracts\FaqRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FaqRepository implements FaqRepositoryInterface
{
    use FileManagerTrait, HasTranslations;

    public function __construct(private readonly Faq $model) {}

    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $faq = $this->model->create(Arr::except($data, ['locales']));

            if (!empty($data['locales'])) {
                $this->syncTranslations($faq, $data['locales'] ?? null);
            }

            return $this->show($faq);
        });
    }


    public function update(Model $faq, array $data): Model
    {
        return DB::transaction(function () use ($faq, $data) {

            $faq->update(Arr::except($data, ['locales']));

            if (isset($data['locales'])) {
                $this->syncTranslations($faq, $data['locales'] ?? null);
            }

            return $this->show($faq->refresh());
        });
    }

    public function show(Model $model): Model
    {
        return $model->load('translations');
    }

    public function delete(Model $model): bool
    {
        return DB::transaction(function () use ($model) {
            return $model->delete();
        });
    }
}
