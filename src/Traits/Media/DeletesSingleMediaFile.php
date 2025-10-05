<?php

namespace HMsoft\Cms\Traits\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

trait DeletesSingleMediaFile
{
    /**
     * Boot the trait.
     * This method is called when a model using this trait is booted.
     */
    public static function bootDeletesSingleMediaFile(): void
    {
        // Listen for the 'deleting' event on the model.
        static::deleting(function (Model $model) {
            // Check if the model has an 'image' relationship (from HasSingleMedia trait).
            if (method_exists($model, 'image') && $model->image) {

                // We resolve the MediaRepository from the service container
                // to avoid injecting it into the model directly.
                $mediaRepository = App::make(\HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface::class);

                // Call the delete method from the repository.
                $mediaRepository->delete($model, $model->image->id);
            }
        });
    }
}
