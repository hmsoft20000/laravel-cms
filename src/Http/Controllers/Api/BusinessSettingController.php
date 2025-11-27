<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Requests\BusinessSetting\{BusinessSettingRequest, UpdateAllBusinessSettingRequest};
use HMsoft\Cms\Repositories\Contracts\BusinessSettingRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class BusinessSettingController extends Controller
{


    public function __construct(private BusinessSettingRepositoryInterface $repo) {}

    public function index(Request $request)
    {
        // $this->authorize('viewAny', \HMsoft\Cms\Models\BusinessSetting::class);

        return  successResponse(
            data: $this->repo->all(),
        );
    }

    public function schema(Request $request)
    {
        // $this->authorize('viewAny', \HMsoft\Cms\Models\BusinessSetting::class);

        $schemaData = Config::get('cms_settings_schema');
        if (!$schemaData) {
            // You can return an error or a default schema
            return errorResponse(message: 'Settings schema not found.');
        }
        return  successResponse(
            data: $schemaData,
        );
    }

    public function update(BusinessSettingRequest $request)
    {
        // $this->authorize('update', resolve(\HMsoft\Cms\Models\BusinessSetting::class));

        $validated = $request->validated();

        $re = $this->repo->updateAll($validated);

        return  successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: $this->repo->all(),
        );
    }

    public function updateAll(UpdateAllBusinessSettingRequest $request)
    {
        // $this->authorize('update', resolve(\HMsoft\Cms\Models\BusinessSetting::class));

        $validated = $request->validated();

        // Process each business setting item in the array
        foreach ($validated as $settingData) {
            $this->repo->updateAll($settingData);
        }

        return successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: $this->repo->all(),
        );
    }
}
