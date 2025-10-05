<?php

namespace HMsoft\Cms\Traits\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;

trait DeletesAllMedia
{
    /**
     * Boot the trait to listen for the 'deleting' event.
     */
    public static function bootDeletesAllMedia(): void
    {
        static::deleting(function (Model $model) {
            // Check if the model has a 'media' relationship and if it has any media items.
            if (method_exists($model, 'media') && $model->media()->exists()) {

                $mediaRepository = App::make(MediaRepositoryInterface::class);

                // We fetch the media items before deleting to avoid issues.
                // It's important to loop because the repository's delete method
                // also handles physical file deletion.
                $mediaItems = $model->media()->get();

                foreach ($mediaItems as $medium) {
                    $mediaRepository->delete($model, $medium->id);
                }
            }
        });
    }
}
