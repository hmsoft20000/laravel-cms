<?php

namespace HMsoft\Cms\Http\Requests\Shop;

use HMsoft\Cms\Http\Requests\MyRequest;

class UpdateAllItemRequest extends MyRequest
{
    public function authorize(): bool
    {
        return true; // أضف منطق الصلاحيات (Permissions) هنا
    }

    public function rules(): array
    {
        // يتم التعامل مع التحقق الفردي لكل عنصر
        // داخل الـ controller's updateAll loop، تمامًا مثل BlogController.
        //
        return [
            '*.id' => 'required|integer|exists:items,id' // قاعدة بسيطة للتأكد من أن الـ ID موجود
        ];
    }
}
