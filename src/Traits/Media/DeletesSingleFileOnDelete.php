<?php

namespace HMsoft\Cms\Traits\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

trait DeletesSingleFileOnDelete
{
    public static function bootDeletesSingleFileOnDelete(): void
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'file') && $model->file) {
                $mediaRepository = App::make(\HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface::class);
                $mediaRepository->delete($model, $model->file->id);
            }
        });
    }
}
