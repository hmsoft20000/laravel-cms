<?php

namespace HMsoft\Cms\Traits\Media;

use HMsoft\Cms\Repositories\Contracts\MediaRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

trait HandlesSingleFile
{
    protected function syncSingleFile(Model $model, array $data, MediaRepositoryInterface $mediaRepository): void
    {
        $currentFile = $model->file;

        if (!empty($data['delete_file']) && $currentFile) {
            $mediaRepository->delete($model, $currentFile->id);
            return;
        }

        if (isset($data['file'])) {
            if ($currentFile) {
                $mediaRepository->delete($model, $currentFile->id);
            }
            $mediaRepository->store($model, ['media' => [['file' => $data['file']]]]);
        }
    }
}
