<?php

namespace HMsoft\Cms\Http\Controllers\Api;

use HMsoft\Cms\Enums\FilterFnsEnum;
use HMsoft\Cms\Http\Requests\ContactUs\{Store, Update, Delete, DeleteAll, ReplyRequest};
use HMsoft\Cms\Http\Controllers\Controller;
use HMsoft\Cms\Http\Resources\Api\ContactUsConversationResource;
use HMsoft\Cms\Http\Resources\Api\ContactUsResource;
use HMsoft\Cms\Models\ContactUs\ContactUs;
use HMsoft\Cms\Repositories\Contracts\ContactUsRepositoryInterface;
use HMsoft\Cms\Services\Filters\AutoFilterAndSortService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactUsController extends Controller
{

    public function __construct(
        private readonly ContactUsRepositoryInterface  $repo,
    ) {}

    public function conversations(Request $request)
    {

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: ContactUs::class,
            beforeOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {},
            extraOperation: function (\Illuminate\Database\Eloquent\Builder &$query, $option) {

                $filterKeys = $option['filterKeys'];

                $query->select(
                    'email as sender_email', // العمود الذي سنقوم بالتجميع بناءً عليه
                    DB::raw('COUNT(*) as message_count'),
                    DB::raw('MAX(id) as id'), // احصل على ID أحدث رسالة
                    DB::raw('MAX(created_at) as last_message_at'),
                    DB::raw('MAX(CAST(is_starred AS UNSIGNED)) as is_starred'),
                    DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(name ORDER BY created_at DESC), ',', 1) as sender_name"),
                    DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(subject ORDER BY created_at DESC), ',', 1) as subject"),
                    DB::raw("SUBSTRING_INDEX(GROUP_CONCAT(message ORDER BY created_at DESC), ',', 1) as snippet"),
                    DB::raw("CASE 
                        WHEN SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) > 0 
                        THEN 'unread' 
                        ELSE 'read' 
                    END as status")
                );

                if ($filterKeys->has('status')) {
                    /** @var ColumnFilterData $statusFilter */
                    $statusFilter = $filterKeys->get('status')[0];
                    $filterValue  = $statusFilter->value; // 'unread', 'starred', etc.
                    switch ($statusFilter->filterFns) {
                        case FilterFnsEnum::equals:
                            $query->havingRaw("
                            (CASE 
                                WHEN SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) > 0 
                                THEN 'unread' 
                                ELSE 'read' 
                            END) = ?
                        ", [$filterValue]);
                            break;

                        case FilterFnsEnum::notEquals:
                            $query->havingRaw("
                            (CASE 
                                WHEN SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) > 0 
                                THEN 'unread' 
                                ELSE 'read' 
                            END) != ?
                        ", [$filterValue]);
                            break;

                        case FilterFnsEnum::in:
                            if (is_array($filterValue) && count($filterValue) > 0) {
                                $placeholders = implode(',', array_fill(0, count($filterValue), '?'));
                                $query->havingRaw("
                            (CASE 
                                WHEN SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) > 0 
                                THEN 'unread' 
                                ELSE 'read' 
                            END) IN ($placeholders)
                        ", $filterValue);
                            }
                            break;

                        case FilterFnsEnum::notIn:
                            if (is_array($filterValue) && count($filterValue) > 0) {
                                $placeholders = implode(',', array_fill(0, count($filterValue), '?'));
                                $query->havingRaw("
                            (CASE 
                                WHEN SUM(CASE WHEN status = 'unread' THEN 1 ELSE 0 END) > 0 
                                THEN 'unread' 
                                ELSE 'read' 
                            END) NOT IN ($placeholders)
                        ", $filterValue);
                            }
                            break;
                    }
                }

                if ($filterKeys->has('is_starred')) {
                    /** @var ColumnFilterData $starredFilter */
                    $starredFilter = $filterKeys->get('is_starred')[0];
                    $isStarred     = filter_var($starredFilter->value, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

                    switch ($starredFilter->filterFns) {
                        case FilterFnsEnum::equals:
                            $query->havingRaw("MAX(CAST(is_starred AS UNSIGNED)) = ?", [$isStarred]);
                            break;

                        case FilterFnsEnum::notEquals:
                            $query->havingRaw("MAX(CAST(is_starred AS UNSIGNED)) != ?", [$isStarred]);
                            break;
                    }
                }

                $query->groupBy('email');

                $query->orderBy('last_message_at', 'desc');
            },
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            $item['snippet'] = mb_substr($item['snippet'], 0, 100, 'UTF-8');
            return resolve(ContactUsConversationResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function index(Request $request)
    {

        $result =  AutoFilterAndSortService::dynamicSearchFromRequest(
            model: ContactUs::class,
        );

        $result['data'] =  collect($result['data'])->map(function ($item) {
            return resolve(ContactUsResource::class, ['resource' => $item])->withFields(request()->get('fields'));
        })->all();

        return  successResponse(
            data: $result['data'],
            pagination: $result['pagination'],
        );
    }

    public function show(ContactUs $message)
    {
        $message->load(['translations']);
        return  successResponse(
            data: resolve(ContactUsResource::class, ['resource' => $message])->withFields(request()->get('fields'))
        );
    }

    public function store(Store $request)
    {
        $validated = $request->validated();
        $message = $this->repo->store($validated);

        return successResponse(
            message: __('cms.contact.message_sent_success'),
            data: resolve(ContactUsResource::class, ['resource' => $message])->withFields(request()->get('fields'))

        );
    }

    public function update(Update $request, ContactUs $message)
    {
        $validated = $request->validated();
        $message = $this->repo->update($message, $validated);
        return  successResponse(
            message: translate('cms.messages.updated_successfully'),
            data: resolve(ContactUsResource::class, ['resource' => $message])->withFields(request()->get('fields'))
        );
    }

    public function destroy(Delete $request, ContactUs $message)
    {
        $this->repo->destroy($message);
        return  successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }

    public function destroyAll(DeleteAll $request)
    {
        $validated = $request->validated();
        $this->repo->destroyAll($validated['ids']);
        return  successResponse(
            message: translate('cms.messages.deleted_successfully'),
        );
    }


    public function reply(ReplyRequest $request, ContactUs $message)
    {
        try {
            // Delegate all the logic to the repository
            $newReplyMessage = $this->repo->replyToMessage(
                $request->validated(),
                $message
            );

            return successResponse(
                message: translate('contact.messages.reply_success'),
                data: [
                    'message' => resolve(ContactUsResource::class, ['resource' => $newReplyMessage])->withFields(request()->get('fields')),
                ],
            );
        } catch (\Exception $e) {
            // The repository might throw an exception (e.g., mail server is down)
            report($e);
            return errorResponse(
                message: translate('contact.messages.reply_failed'),
                state: 500
            );
        }
    }
}
