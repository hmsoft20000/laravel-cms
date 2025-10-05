<?php

namespace HMsoft\Cms\Traits\Translations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasTranslations
{

    /**
     * Sync translations for a given attachment model (e.g., Feature, Download).
     *
     * @param Model $model The attachment instance.
     * @param array|null $localesData The array of translation data.
     * @return void
     */
    protected function syncTranslations(Model $model, ?array $localesData = null): void
    {
        if ($localesData === null) return;
        if (!method_exists($model, 'translations')) {
            return;
        }

        $locales = collect($localesData)->pluck('locale')->toArray();
        $model->translations()
            ->whereNotIn('locale', $locales)
            ->delete();


        foreach ($localesData ?? [] as $localeData) {
            $model->translations()->updateOrCreate(
                ['locale' => $localeData['locale']],
                Arr::except($localeData, 'locale')
            );
        }
    }
}
