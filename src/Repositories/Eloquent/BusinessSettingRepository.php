<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Models\BusinessSetting;
use HMsoft\Cms\Repositories\Contracts\BusinessSettingRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class BusinessSettingRepository implements BusinessSettingRepositoryInterface
{

    use FileManagerTrait;


    public static $defaultImages = [
        'default_brand_image',
        'default_category_image',
        'default_property_image',
        'default_city_image',
        'default_item_image',
        'default_service_image',
        'default_feature_image',
        'default_statistic_image',
        'default_blog_image',
        'default_rewad_image',
        'default_partner_image',
        'default_portfolio_image',
        'default_organization_image',
        'default_team_image',
        'default_slider_image',
        'default_portfolio_image',
        'default_user_image',
    ];

    public function __construct(protected BusinessSetting $model) {}

    public function find(string $id): ?BusinessSetting
    {
        return $this->model->where('id', $id)->first();
    }

    public function all(): array
    {

        $envAppName =  strtolower(config('app.name'));

        $web_config = [];
        $this->model->select(['type', 'value'])->get()->each(function ($item) use (&$web_config) {
            $web_config[$item['type']] = $item['value'];
        });

        $assetDisk = storageDisk('public');

        $defaultImageFolderUrl = storageDisk('public')->url(DEFAULTS_IMAGE_NAME);

        if (isset($web_config['company_logo'])) {
            $web_config['company_logo_url'] = $assetDisk->url($web_config['company_logo']);
        }
        if (isset($web_config['company_fav_icon'])) {
            $web_config['company_fav_icon_url'] = $assetDisk->url($web_config['company_fav_icon']);
        }
        if (isset($web_config['footer_logo'])) {
            $web_config['footer_logo_url'] = $assetDisk->url($web_config['footer_logo']);
        }

        if (isset($web_config['about_us_image'])) {
            $web_config['about_us_image'] = $assetDisk->url(ABOUT_IMAGE_NAME . '/' . $web_config['about_us_image']);
        }

        foreach (static::$defaultImages as $key) {
            if (isset($web_config[$key])) {
                $image = $defaultImageFolderUrl . "/" . $web_config[$key];
                if (filter_var($image, FILTER_VALIDATE_URL)) {
                    $web_config[$key] = $image;
                }
            }
        }


        if (isset($web_config['cookie_setting'])) {
            $web_config['cookie_setting'] = (array) json_decode($web_config['cookie_setting']);
        }

        $web_config['cookie_popup_banner_name'] = $envAppName . '_popup_banner';
        $web_config['cookie_consent_name'] = $envAppName . '_cookie_consent';
        $web_config['cookie_order_by_whatsapp_name'] = $envAppName . 'order_by_whatsapp';

        $web_config['asset_url'] = $assetDisk->url("");


        return $web_config;
    }

    public function update(string $id, array $data): bool
    {
        $model = $this->model->where('id', $id)->update($data);
        $this->refreshCache();
        return $model;
    }

    public function updateAll(array $data)
    {
        $imagesKeys = [
            'company_logo',
            'company_fav_icon',
            'footer_logo',
            ...static::$defaultImages
        ];
        $updateData = collect($data)->except($imagesKeys)->toArray();

        foreach ($imagesKeys as $key) {
            $deleteKey =  $key . "_delete_image";
            $oldImage = BusinessSetting::where(['type' => $key])->select('value')->first()?->value;

            // Check if an image is provided in the request
            if (array_key_exists($key, $data) && $data[$key] instanceof \Illuminate\Http\UploadedFile) {
                $fileName = $this->updateAndUploadFile($key, $data[$key], $oldImage);
                $updateData[$key] = $fileName;
            }
            // Check for an explicit deletion request
            else if (array_key_exists($deleteKey, $data) && $data[$deleteKey] == true) {
                if ($oldImage) {
                    $this->deleteFileByFolderAndName($key, $oldImage);
                }
                $updateData[$key] = null;
            }
        }

        foreach ($updateData as $key => $value) {
            $model = $this->model->where('type', $key)->update(['value' => $value]);
            $this->refreshCache();
            return $model;
        }
    }

    public function updateAndUploadFile($key, $file, string|null $oldFileName = null): bool|string
    {
        $folder = static::getImageFolder($key);
        $fileName = $oldFileName
            ? $this->updateFile("$folder/", $oldFileName, $file, disk: 'public')
            : $this->upload("$folder/", $file, disk: 'public');

        $this->refreshCache();

        return $fileName;
    }
    public function deleteFileByFolderAndName($key, $file)
    {
        $folder = static::getImageFolder($key);
        $this->deleteFile($folder . '/' . $file, disk: 'public');

        $this->refreshCache();

        return true;
    }
    /** 
     * upload image or delete it.
     * @return string|null image  url or null
     */
    public function handelImage($key, $image, $deleteImage): string|null
    {
        $oldImage =  BusinessSetting::where(['type' => $key])->select('value')->first()?->value;
        $hasOldImage = is_null($oldImage);

        if (isset($image) && $deleteImage == false) {
            // new or update
            $fileName = $this->proceedImage($key, file: $image, oldFileName: $oldImage);
            $image = $fileName;
        } else if ($hasOldImage == true && $deleteImage == true) {
            // delete image.
            $this->proceedImageDelete($key, $oldImage);
            $image = null;
        }

        $this->refreshCache();

        return $image;
    }

    public static function getImageFolder($key)
    {
        $folder = '';
        if (str_starts_with($key, 'default_')) {
            $folder = DEFAULTS_IMAGE_NAME;
        } else {
            switch ($key) {
                case 'company_logo':
                case 'company_fav_icon':
                case 'footer_logo':
                    $folder = IMAGE_NAME;
                    break;
                case 'about_us_image':
                    $folder = ABOUT_IMAGE_NAME;
                case 'our_history_image':
                    $folder = OUR_HISTORY_IMAGE_NAME;
                case 'our_mission_image':
                    $folder = OUR_MISSION_IMAGE_NAME;
                case 'our_vision_image':
                    $folder = OUR_VISION_IMAGE_NAME;
                    break;
                case 'privacy_policy_image':
                    $folder = PRIVACY_POLICY_IMAGE_NAME;
                    break;
                case 'terms_of_service_image':
                    $folder = TERM_OF_SERVICE_IMAGE_NAME;
                    break;
                case 'term_and_condition_image':
                    $folder = TERM_AND_CONDITION_IMAGE_NAME;
                    break;
                default:
                    # code...
                    break;
            }
        }
        return $folder;
    }

    public function proceedImage($key, $file, string|null $oldFileName = null): bool|string
    {
        $folder = static::getImageFolder($key);
        return $oldFileName
            ? $this->updateFile("$folder/", $oldFileName, $file, disk: 'public')
            : $this->upload("$folder/", $file, disk: 'public');
    }

    public function proceedImageDelete($key, $file)
    {
        $folder = static::getImageFolder($key);

        $this->deleteFile($folder . '/' . $file, disk: 'public');

        $this->refreshCache();

        return true;
    }

    public function refreshCache(): mixed
    {
        $web_config = [];
        if (Schema::hasTable('business_settings')) {
            Cache::forget('business_settings');
            $web_config = Cache::rememberForever('business_settings', function () {
                return $this->all();
            });
        }
        return $web_config;
    }
}
