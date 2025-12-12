<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Resources\Api\OrganizationResource;
use HMsoft\Cms\Models\Organizations\Organization;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NestedOrganizationController extends Controller
{
    /**
     * Display a listing of the organizations associated with the owner.
     * عرض المنظمات (رعاة، شركاء، إلخ) المرتبطة بالعنصر.
     */
    public function index(Request $request, Model $owner): JsonResponse
    {
        // جلب النوع (sponsor, partner, agent) من إعدادات المسار
        $targetRole = $request->route()->parameter('type') ?? null;
        $result = AutoFilterAndSortService::dynamicSearchFromRequest(
            model: resolve(Organization::class),
            extraOperation: function ($query) use ($owner, $targetRole) {
                // [تعديل هام]: الربط مع جدول organization_links بناءً على Trait Linkable
                $query->join('organization_links', 'organizations.id', '=', 'organization_links.organization_id')
                    ->where('organization_links.linkable_id', $owner->id)
                    ->where('organization_links.linkable_type', $owner->getMorphClass());

                // إذا كان المسار محدداً بنوع معين (مثل sponsors)، نقوم بفلترة الدور (role)
                if ($targetRole) {
                    $query->where('organization_links.role', $targetRole);
                }

                // تحديد الأعمدة لتجنب تضارب الأسماء بعد الـ Join
                $query->select('organizations.*');

                // تحميل العلاقات المطلوبة
                $query->with(['translations', 'image']);
            },
        );

        $result['data'] = collect($result['data'])->map(function ($item) {
            return resolve(OrganizationResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    /**
     * Attach an existing organization to the owner with the specific role.
     * ربط منظمة موجودة بالعنصر مع تحديد الدور تلقائياً.
     */
    public function store(Request $request, Model $owner): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
        ]);

        // تحديد الدور بناءً على المسار (sponsor, partner...)
        $role = $request->route()->parameter('type') ?? 'general';

        $organizationId = $request->input('organization_id');
        $organization = Organization::findOrFail($organizationId);

        // استخدام العلاقة organizations الموجودة في Linkable Trait
        // يتم حفظ الدور (role) في الجدول الوسيط
        $owner->organizations()->syncWithoutDetaching([
            $organizationId => ['role' => $role]
        ]);

        return successResponse(
            message: translate('cms.messages.added_successfully'),
            data: resolve(OrganizationResource::class, ['resource' => $organization])->withFields(request()->get('fields')),
        );
    }

    /**
     * Detach the organization from the owner.
     * فك الارتباط.
     */
    public function destroy(Model $owner, Organization $organization): JsonResponse
    {
        // التحقق من نوع العلاقة الحالية قبل الحذف (اختياري ولكن مفضل)
        $targetRole = request()->route()->parameter('type') ?? null;

        if ($targetRole) {
            // حذف السجل الذي يحمل هذا الدور فقط لهذا العنصر
            $owner->organizations()
                ->wherePivot('role', $targetRole)
                ->detach($organization->id);
        } else {
            // حذف أي ارتباط بهذه المنظمة بغض النظر عن الدور
            $owner->organizations()->detach($organization->id);
        }

        return successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }
}
