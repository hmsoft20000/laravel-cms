<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Helpers\UserModelHelper;
use HMsoft\Cms\Repositories\Contracts\UserRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserRepository implements UserRepositoryInterface
{
    use FileManagerTrait;

    protected $model;

    public function __construct()
    {
        $this->model = UserModelHelper::getUserModelClass();
    }

    public function store(array $data): Model
    {
        $createData = collect($data);

        DB::beginTransaction();
        $createdModel = $this->model::create($createData->toArray());
        DB::commit();

        return $createdModel;
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $updateData = Arr::except($data, []);

        DB::beginTransaction();
        $model->update($updateData);
        DB::commit();

        $model->refresh();
        return $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        $filePath = cmsImageDir('users') . '/' . $model->image;
        $this->deleteFile(filePath: $filePath, disk: 'public');
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        $filePath = cmsImageDir('users') . '/' . $model->image;
        $this->deleteFile(filePath: $filePath, disk: 'public');
        return true;
    }

    public function checkOldPassword(Model $user, string $oldPassword): bool
    {
        return Hash::check($oldPassword, $user->password);
    }

    public function updateInfo(Model $user, array $data): bool
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $path = $data['image']->store('images', 'public');
            $data['image'] = $path;
        } else {
            unset($data['image']);
        }

        return $user->update($data);
    }
    public function updatePassword(Model $user, string $newPassword): bool
    {
        $user->password = Hash::make($newPassword);
        return $user->save();
    }

    public function proceedImage($file, string|null $oldFileName = null): bool|string
    {
        $folderPath = cmsImageDir('users');
        if ($oldFileName) {
            $image =  $this->updateFile(dir: $folderPath . '/', oldFile: $oldFileName, file: $file, disk: 'public');
        } else {
            $image =  $this->upload(dir: $folderPath . '/', file: $file, disk: 'public');
        }
        return $image;
    }

    public function proceedImageDelete($file)
    {
        $folderPath = cmsImageDir('users');
        return  $this->deleteFile(filePath: $folderPath . '/' . $file, disk: 'public');
    }
}
