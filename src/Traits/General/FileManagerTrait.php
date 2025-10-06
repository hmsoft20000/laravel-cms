<?php

namespace HMsoft\Cms\Traits\General;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait FileManagerTrait
{

    /**
     * @param string $dir
     * @param $oldFile
     * @param $file
     * @param string $imageFormat
     * @param string $disk
     * @return string
     */
    public function updateFile(string $dir, $oldFile, $file, string|null $imageFormat = null, string $disk = "public"): string
    {
        if (Storage::disk($disk)->exists($dir . $oldFile)) {
            Storage::disk($disk)->delete($dir . $oldFile);
        }
        return $this->upload(dir: $dir, imageFormat: $imageFormat, file: $file, disk: $disk);
    }

    /**
     * @param string $dir
     * @param $file
     * @param string $imageFormat
     * @param string $disk
     * @return string
     */
    public function upload(string $dir, $file, string|null $imageFormat = null, string $disk = "public"): string
    {
        $path = $dir;
        $fileExtension = $file->extension();
        // $fileExtension = $file->getClientOriginalExtension();
        $imageFormat = $imageFormat ?? cmsFileSetting('image_format');

        $fileName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $fileExtension;

        if (!Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->makeDirectory($path);
        }

        if (in_array($fileExtension, ['jpg', 'jpeg', 'bmp', 'png'])) {

            $fileFormate = $imageFormat == null ? $fileExtension : $imageFormat;
            try {
                $manager = new ImageManager(new Driver());
                $image = $manager->read($file);
                $imageMedium =  $image->encodeByExtension($fileFormate, 30);
                Storage::disk($disk)->put($path . $fileName, $imageMedium);
            } catch (\Throwable $th) {
                info('FileManagerTrait', [
                    'th' => $th->getMessage()
                ]);
                Storage::disk('public')->put($path . $fileName, file_get_contents($file));
            }
        } else {
            Storage::disk($disk)->put($path . $fileName, file_get_contents($file));
        }
        return $fileName;
    }


    /**
     * @param string $filePath
     * @param string $disk
     * @return array
     */
    protected function  deleteFile(string $filePath, string $disk = "public"): array
    {
        if (Storage::disk($disk)->exists($filePath)) {
            Storage::disk($disk)->delete($filePath);
        }
        return [
            'success' => true,
            'message' => trans('Removed_successfully')
        ];
    }
}
