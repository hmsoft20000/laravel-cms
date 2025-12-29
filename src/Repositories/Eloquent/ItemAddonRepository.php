<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shop\Item;
use HMsoft\Cms\Models\Shop\ItemAddon;
use HMsoft\Cms\Repositories\Contracts\ItemAddonRepositoryInterface;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ItemAddonRepository implements ItemAddonRepositoryInterface
{
    use HasTranslations;

    public function store(Item $item, array $data): Model
    {
        return DB::transaction(function () use ($item, $data) {
            // 1. إنشاء الإضافة وربطها بالمنتج
            /** @var ItemAddon $addon */
            $addon = $item->addons()->create(
                Arr::except($data, ['locales', 'options'])
            );

            // 2. حفظ الترجمات
            $this->syncTranslations($addon, $data['locales'] ?? null);

            // 3. حفظ الخيارات (Options) إذا وجدت
            if (!empty($data['options'])) {
                $this->syncOptions($addon, $data['options']);
            }

            return $addon->load(['translations', 'options.translations']);
        });
    }

    public function update(ItemAddon $addon, array $data): Model
    {
        return DB::transaction(function () use ($addon, $data) {
            // 1. تحديث البيانات الأساسية
            $addon->update(Arr::except($data, ['locales', 'options']));

            // 2. تحديث الترجمات
            $this->syncTranslations($addon, $data['locales'] ?? null);

            // 3. تحديث الخيارات (إضافة/تعديل/حذف)
            if (isset($data['options'])) {
                $this->syncOptions($addon, $data['options']);
            }

            return $addon->refresh()->load(['translations', 'options.translations']);
        });
    }

    public function delete(ItemAddon $addon): bool
    {
        return $addon->delete();
    }

    /**
     * دالة مساعدة لمزامنة خيارات الإضافة
     * تم نقل المنطق من HandlesAddonSyncing
     */
    protected function syncOptions(ItemAddon $addon, array $optionsData): void
    {
        $existingIds = [];
        $sortOrder = 1;

        foreach ($optionsData as $optionData) {
            $optionId = $optionData['id'] ?? null;

            // نستخدم updateOrCreate للتعامل مع الإضافة والتعديل في آن واحد
            $option = $addon->options()->updateOrCreate(
                ['id' => $optionId],
                Arr::except($optionData, ['id', 'locales']) + ['sort_number' => $sortOrder++]
            );

            // مزامنة ترجمات الخيار
            $this->syncTranslations($option, $optionData['locales'] ?? null);

            $existingIds[] = $option->id;
        }

        // حذف الخيارات التي لم تعد موجودة في الـ Request
        $addon->options()->whereNotIn('id', $existingIds)->delete();
    }
}
