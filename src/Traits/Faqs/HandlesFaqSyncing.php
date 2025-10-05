<?php

namespace HMsoft\Cms\Traits\Faqs;

use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HandlesFaqSyncing
{
    use HasTranslations;

    protected function syncFaqs(Model $model, ?array $faqsData = null): void
    {
        if (!method_exists($model, 'faqs')) return;
        if ($faqsData === null) return;
        $existingIds = $model->faqs()->pluck('id')->toArray();
        $incomingIds = Arr::pluck(Arr::whereNotNull($faqsData, 'id'), 'id');
        $idsToDelete = array_diff($existingIds, $incomingIds);

        if (!empty($idsToDelete)) {
            $model->faqs()->whereIn('id', $idsToDelete)->delete();
        }

        foreach ($faqsData ?? [] as $faqData) {
            $faq = $model->faqs()->updateOrCreate(
                ['id' => $faqData['id'] ?? null],
                Arr::except($faqData, ['id', 'locales'])
            );
            $this->syncTranslations($faq, $faqData['locales'] ?? null);
        }
    }
}
