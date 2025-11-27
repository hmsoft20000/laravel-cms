<?php

namespace HMsoft\Cms\Traits\Shop;

use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Models\Shop\ItemAddon;
use HMsoft\Cms\Models\Shop\ItemAddonOption;
use HMsoft\Cms\Traits\Translations\HasTranslations; //
use Illuminate\Support\Arr;

trait HandlesAddonSyncing
{
    use HasTranslations; // لاستخدامه في مزامنة ترجمات الإضافات والخيارات

    protected function syncAddons(Item $item, ?array $addonsData): void
    {
        if ($addonsData === null) return;

        $existingIds = [];
        $sortOrder = 1;

        foreach ($addonsData as $addonData) {
            $addonId = $addonData['id'] ?? null;

            // 1. إنشاء أو تحديث الإضافة (Addon)
            $addon = $item->addons()->updateOrCreate(
                ['id' => $addonId],
                Arr::except($addonData, ['id', 'locales', 'options', 'sort_number']) + ['sort_number' => $sortOrder++]
            );

            // 2. مزامنة ترجمات الإضافة
            $this->syncTranslations($addon, $addonData['locales'] ?? null);

            // 3. مزامنة خيارات الإضافة (Addon Options)
            $this->syncAddonOptions($addon, $addonData['options'] ?? []);

            $existingIds[] = $addon->id;
        }

        // حذف الإضافات القديمة التي لم يتم إرسالها
        $item->addons()->whereNotIn('id', $existingIds)->delete();
    }

    protected function syncAddonOptions(ItemAddon $addon, array $optionsData): void
    {
        $existingOptionIds = [];
        $optionSortOrder = 1;

        foreach ($optionsData as $optionData) {
            $optionId = $optionData['id'] ?? null;

            // 1. إنشاء أو تحديث الخيار (Option)
            $option = $addon->options()->updateOrCreate(
                ['id' => $optionId],
                Arr::except($optionData, ['id', 'locales', 'sort_number']) + ['sort_number' => $optionSortOrder++]
            );

            // 2. مزامنة ترجمات الخيار
            $this->syncTranslations($option, $optionData['locales'] ?? null);

            $existingOptionIds[] = $option->id;
        }

        // حذف الخيارات القديمة
        $addon->options()->whereNotIn('id', $existingOptionIds)->delete();
    }
}
