<?php

namespace HMsoft\Cms\Traits\Downloads;

use HMsoft\Cms\Models\Shared\Download;
use HMsoft\Cms\Traits\Translations\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HandlesDownloadSyncing
{

    use HasTranslations;


    /**
     * Sync downloads for a given model.
     *
     * @param Model $model The model to sync downloads for.
     * @param array|null $downloadsData The downloads to sync.
     * @return void
     */
    protected function syncDownloads(Model $model, ?array $downloadsData = null): void
    {
        // 1. Check if the model has the 'downloads' relationship
        if (!method_exists($model, 'downloads')) {
            return;
        }

        // 2. If data is null, do nothing.
        // If data is an *empty array*, it means "delete all".
        if ($downloadsData === null) {
            return;
        }

        $idsToKeep = [];

        // 3. Create or Update Download items and their translations
        foreach ($downloadsData as $downloadData) {
            // Data for the 'download_items' table
            $itemData = Arr::except($downloadData, ['id', 'locales']);

            // Data for the 'download_item_translations' table
            $localesData = $downloadData['locales'] ?? null;

            // Use updateOrCreate on the Download model itself
            $download = Download::updateOrCreate(
                ['id' => $downloadData['id'] ?? null], // Match by ID
                $itemData  // Data to create or update
            );

            // Sync translations (assumes $download model uses Translatable trait)
            if ($localesData) {
                // The syncTranslations method is ON the $download model
                $this->syncTranslations($download, $localesData);
            }

            // Add this ID to the list of items to be attached
            $idsToKeep[] = $download->id;
        }

        // 4. Get IDs of items to delete
        // These are items that were attached but are NOT in the new $idsToKeep list.
        $existingIds = $model->downloads()->pluck('download_items.id')->toArray();
        $idsToDelete = array_diff($existingIds, $idsToKeep);

        // 5. Delete the old Download items
        // This follows your original code's intent to DELETE, not just detach.
        // WARNING: This is dangerous if downloads are shared between models.
        if (!empty($idsToDelete)) {
            // The Download model (using Translatable) should handle
            // deleting its own translations automatically on delete.
            Download::whereIn('id', $idsToDelete)->delete();
        }

        // 6. Sync the pivot table ('downloads')
        // This will:
        // - Attach any IDs in $idsToKeep that aren't already attached.
        // - Detach any IDs (like $idsToDelete) that are attached but not in $idsToKeep.
        $model->downloads()->sync($idsToKeep);
    }
}
