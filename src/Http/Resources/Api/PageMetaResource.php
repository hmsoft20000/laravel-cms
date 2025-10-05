<?php

namespace HMsoft\Cms\Http\Resources\Api;

use HMsoft\Cms\Http\Resources\BaseJsonResource;
use Illuminate\Http\Request;

class PageMetaResource extends BaseJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function resolveData(Request $request)
    {

        $data = \Illuminate\Http\Resources\Json\JsonResource::toArray($request);

        $data = collect($data)
            ->only([
                'id',
                'name',
                'translations',
                'created_at',
                'updated_at',
            ])->all();

        if (array_key_exists('translations', $data)) {
            $data['translations'] = collect($data['translations'])->mapWithKeys(function ($translation) {
                return [$translation['locale'] => [
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                    'keywords' => $translation['keywords'],
                ]];
            })->all();
        }

        return $data;
    }
}
