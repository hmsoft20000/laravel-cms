<?php

namespace HMsoft\Cms\Traits\Media;

use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;

trait HandlesSingleMedia
{

    use FileManagerTrait;

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
        $usesColumn = Schema::hasColumn($model->getTable(), 'image');
        if ($usesColumn) {
            $currentImageValue = $model->getAttribute('image');

            if (!empty($data['delete_image']) && $currentImageValue) {
                $this->deleteFile(CATEGORY_IMAGE_NAME . '/' . $currentImageValue);
                $model->image = null;
                $model->save();
                return;
            }

            if (isset($data['image'])) {
                if ($data['image'] instanceof UploadedFile) {
                    if ($currentImageValue) {
                        $this->deleteFile(CATEGORY_IMAGE_NAME . '/' . $currentImageValue);
                    }
                    $fileName = $data['image']->hashName();
                    $data['image']->store(CATEGORY_IMAGE_NAME, 'public');
                    $model->image = $fileName;
                } elseif (is_string($data['image'])) {
                    $model->image = $data['image'];
                }
                $model->save();
            }
            return;
        }

        if ($model->isRelation('image')) {
            $currentImage = $model->image;

            if (!empty($data['delete_image']) && $currentImage) {
                $mediaRepository->delete($model, $currentImage->id);
                return;
            }

            // if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if (isset($data['image'])) {
                if ($currentImage) {
                    $mediaRepository->delete($model, $currentImage->id);
                }
                $mediaRepository->store($model, [
                    'media' => [['file' => $data['image'], 'is_default' => true]]
                ]);
            }
        }
    }


    /**
     * Syncs a single image for a model.
     *
     * @param Model $model The model instance.
     * @param array $data The request data array.
     * @param MediaRepositoryInterface $mediaRepository The repository to handle media operations.
     * @return void
     * @deprecated
     */
    protected function syncSingleImageDeprecated(Model $model, array $data, MediaRepositoryInterface $mediaRepository): void
    {
        $currentImage = $model->image;


        if (!empty($data['delete_image'])) {
            if ($currentImage instanceof Model) {
                $mediaRepository->delete($model, $currentImage->id);
            } else {
                $model->image = null;
                $model->save();
            }
            return;
        }

        if (isset($data['image'])) {
            $newImage = $data['image'];

            if ($newImage instanceof UploadedFile) {
                if ($currentImage instanceof Model) {
                    $mediaRepository->delete($model, $currentImage->id);
                }
                $mediaRepository->store($model, [
                    'media' => [['file' => $newImage, 'is_default' => true]]
                ]);
            } elseif (is_string($newImage)) {
                if ($currentImage instanceof Model) {
                    $mediaRepository->delete($model, $currentImage->id);
                }
                $model->image = $newImage;
                $model->save();
            }
        }
        // if (isset($data['image'])) {
        //     if ($currentImage) {
        //         $mediaRepository->delete($model, $currentImage->id);
        //     }

        //     $mediaRepository->store($model, [
        //         'media' => [
        //             [
        //                 'file' => $data['image'],
        //                 'is_default' => true
        //             ]
        //         ]
        //     ]);
        // }
    }
}
