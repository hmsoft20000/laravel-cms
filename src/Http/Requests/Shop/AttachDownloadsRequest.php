<?php

namespace HMsoft\Cms\Http\Requests\Shop;

use HMsoft\Cms\Http\Requests\MyRequest;

class AttachDownloadsRequest extends MyRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // يمكنك إضافة منطق الصلاحيات (Permissions) هنا لاحقًا
        // على سبيل المثال: return $this->user()->can('items.attachDownloads');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'download_item_ids' => 'required|array',

            // نتأكد أن كل عنصر في المصفوفة هو رقم صحيح
            // وموجود فعليًا في جدول 'download_items'
            //
            'download_item_ids.*' => 'required|integer|exists:download_items,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        // يمكنك إضافة ملف ترجمة مخصص إذا أردت
        return [
            'download_item_ids.required' => 'The download item IDs field is required.',
            'download_item_ids.array' => 'The download item IDs must be an array.',
            'download_item_ids.*.exists' => 'One of the selected download items is invalid.',
        ];
    }
}
