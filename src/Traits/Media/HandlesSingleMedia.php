<?php

namespace HMsoft\Cms\Traits\Media;

use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

trait HandlesSingleMedia
{
    /**
     * Syncs a single image for a model.
     *
     * @param Model $model The model instance.
     * @param array $data The request data array.
     * @param MediaRepositoryInterface $mediaRepository The repository to handle media operations.
     * @return void
     */
    protected function syncSingleImage(Model $model, array $data, MediaRepositoryInterface $mediaRepository): void
    {
        $currentImage = $model->image;

        if (!empty($data['delete_image']) && $currentImage) {
            $mediaRepository->delete($model, $currentImage->id);
            return;
        }

        if (isset($data['image'])) {
            if ($currentImage) {
                $mediaRepository->delete($model, $currentImage->id);
            }

            $mediaRepository->store($model, [
                'media' => [
                    ['file' => $data['image']]
                ]
            ]);
        }
    }
}
