<?php

namespace HMsoft\Cms\Traits\General;

use Illuminate\Database\Eloquent\Model;
use HMsoft\Cms\Helpers\UserModelHelper;
use Illuminate\Support\Facades\Auth;

trait CURDTrait
{

    public static function bootCURDTrait()
    {
        static::creating(function (Model $model) {
            if (hasColumn($model, 'created_at')) {
                $model->created_at = now();
            }
            if (hasColumn($model, 'created_by')) {
                $userModelClass = UserModelHelper::getUserModelClass();
                $userModel = new $userModelClass();
                $authUser = Auth::check() ? Auth::user() : null;
                $model->created_by = $authUser?->{$userModel->getKeyName()} ?? null;
            }
        });

        static::updating(function (Model $model) {
            if (hasColumn($model, 'updated_at')) {
                $model->updated_at = now();
            }
            if (hasColumn($model, 'updated_by')) {
                $userModelClass = UserModelHelper::getUserModelClass();
                $userModel = new $userModelClass();
                $authUser = Auth::check() ? Auth::user() : null;
                $model->updated_by = $authUser?->{$userModel->getKeyName()} ?? null;
            }
        });

        static::deleting(function (Model $model) {
            if (hasColumn($model, 'deleted_at')) {
                $model->deleted_at = now();
            }
            if (hasColumn($model, 'deleted_by')) {
                $userModelClass = UserModelHelper::getUserModelClass();
                $userModel = new $userModelClass();
                $authUser = Auth::check() ? Auth::user() : null;
                $model->deleted_by = $authUser?->{$userModel->getKeyName()} ?? null;
            }
        });
    }
}
