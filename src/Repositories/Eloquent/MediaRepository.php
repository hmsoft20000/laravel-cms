<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Shared\Medium;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

class MediaRepository implements MediaRepositoryInterface
{

    use FileManagerTrait;

    /**
     * Upload and store media files for a given owner model.
     * @param Model $owner
     * @param array $data
     * @return Collection
     */
    public function store(Model $owner, array $data): Collection
    {
        $mediaItems = $data['media'] ?? [];
        $mediaCollection = new Collection();

        $lastDefaultIndex = collect($mediaItems)->last(function ($item) {
            return !empty($item['is_default']);
        });

        DB::transaction(function () use ($owner, $mediaItems, &$mediaCollection, $lastDefaultIndex) {
            if ($lastDefaultIndex) {
                $owner->media()->update(['is_default' => false]);
            }

            $sortNumber = $owner->media()->max('sort_number') ?? 0;

            foreach ($mediaItems as $index => $item) {
                $fileOrUrl = $item['file'];
                $filePath = null;
                $fileDetails = [];

                if ($fileOrUrl instanceof UploadedFile) {
                    $ownerType = $owner->getMorphClass();
                    $ownerId = $owner->id;

                    $path = $this->upload("{$ownerType}/{$ownerId}/media/", $fileOrUrl);

                    $filePath = $path;
                    $fileDetails = [
                        'file_name' => $fileOrUrl->getClientOriginalName(),
                        'file_size' => $fileOrUrl->getSize(),
                        'mime_type' => $fileOrUrl->getMimeType(),
                    ];
                } elseif (is_string($fileOrUrl)) {
                    $filePath = $fileOrUrl;
                    $fileDetails['mime_type'] = 'link';
                }

                $isDefault = ($lastDefaultIndex && $item === $lastDefaultIndex);

                $newMedia = $owner->media()->create(array_merge($fileDetails, [
                    'file_path' => $filePath,
                    'is_default' => $isDefault,
                    'sort_number' => $item['sort_number'] ?? ++$sortNumber,
                ]));

                $mediaCollection->push($newMedia);
            }
        });

        return $mediaCollection;
    }

    /**
     * Show a media item
     * @param Model $owner
     * @param Medium $medium
     * @return Model
     */
    public function show(Model $owner, $medium): Model
    {
        $mediumId = is_object($medium) ? $medium->id : $medium;
        return $owner->media()->findOrFail($mediumId);
    }

    /**
     * Update a media item
     * @param Model $owner
     * @param Medium $medium
     * @param array $data
     * @return Medium
     */
    public function update(Model $owner, Medium $medium, array $data): Medium
    {
        DB::transaction(function () use ($owner, $medium, $data) {
            if (!empty($data['is_default']) && !$medium->is_default) {

                $owner->media()->where('id', '!=', $medium->id)->update(['is_default' => false]);
            }
            $medium->update($data);
        });
        return $medium->fresh();
    }


    /**
     * Delete a media item
     * @param Model $owner
     * @param int $mediaId
     * @return void
     */
    public function delete(Model $owner, int $mediaId): void
    {
        DB::transaction(function () use ($owner, $mediaId) {
            $mediumToDelete = $owner->media()->findOrFail($mediaId);

            if ($mediumToDelete->is_default) {
                $nextDefaultCandidate = $owner->media()
                    ->where('id', '!=', $mediumToDelete->id)
                    ->orderBy('sort_number', 'asc')
                    ->orderBy('id', 'asc')
                    ->first();

                if ($nextDefaultCandidate) {
                    $nextDefaultCandidate->update(['is_default' => true]);
                }
            }

            if ($mediumToDelete->mime_type !== 'link') {
                $ownerType = $owner->getMorphClass();
                $ownerId = $owner->id;
                $this->deleteFile("{$ownerType}/{$ownerId}/media/{$mediumToDelete->file_path}");
            }
            $mediumToDelete->delete();
        });
    }

    /**
     * Update all media for a model
     * @param Model $owner
     * @param array $mediaData
     * @return Collection
     */
    public function updateAll(Model $owner, array $mediaData): Collection
    {
        DB::transaction(function () use ($owner, $mediaData) {
            if (collect($mediaData)->where('is_default', true)->isNotEmpty()) {
                $owner->media()->update(['is_default' => false]);
            }

            foreach ($mediaData as $itemData) {
                $medium = $owner->media()->find($itemData['id']);
                if ($medium) {
                    $medium->update($itemData);
                }
            }
        });
        // return $owner->media()->orderBy('sort_number')->get();
        return $owner->media()->get();
    }
}
