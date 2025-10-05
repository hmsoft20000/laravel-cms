<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\Slider\Slider;
use HMsoft\Cms\Repositories\Contracts\SliderRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SliderRepository implements SliderRepositoryInterface
{
    use FileManagerTrait;

    public function __construct(
        private readonly Slider $model,
    ) {}


    public function store(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            if (isset($data['image'])) {
                $fileName = $this->proceedImage(file: $data['image']);
                $data['image'] = $fileName;
            }

            $createData = Arr::except($data, ['locales']);
            $model = $this->model->create($createData);

            $model->translations()->createMany($data['locales'] ?? null);

            return $model;
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {

            // Check for image deletion first
            if (array_key_exists('delete_image', $data) && boolval($data['delete_image'])) {
                if (!is_null($model->image)) {
                    $this->proceedImageDelete($model->image);
                }
                $data['image'] = null;
            }

            // Check for a new image upload
            if (array_key_exists('image', $data) && !is_null($data['image'])) {
                // If there was an old image, the proceedImage function should handle deleting it
                $fileName = $this->proceedImage(file: $data['image'], oldFileName: $model->image);
                $data['image'] = $fileName;
            }

            $model->update(Arr::except($data, ['locales']));

            foreach ($data['locales'] ?? null as $localeData) {
                $this->storeOrUpdateTranslation($model, $localeData);
            }

            return $model->refresh();
        });
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        $this->deleteFile(SLIDER_IMAGE_NAME . '/' . $model->image);
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        $this->deleteFile(SLIDER_IMAGE_NAME . '/' . $model->image);
        return true;
    }

    public function proceedImage($file, string|null $oldFileName = null): bool|string
    {
        $folder = SLIDER_IMAGE_NAME;
        return $oldFileName
            ? $this->updateFile("$folder/", $oldFileName, $file)
            : $this->upload("$folder/", $file);
    }

    public function proceedImageDelete($file)
    {
        return $this->deleteFile(SLIDER_IMAGE_NAME . '/' . $file);
    }




    private function storeOrUpdateTranslation(Slider $model, array $localeData): void
    {
        $locale = $localeData['locale'];
        $translation = $model->translations()->where('locale', $locale)->first();

        if ($translation) {
            $translation->update($localeData);
        } else {
            $model->translations()->create($localeData);
        }
    }
}
