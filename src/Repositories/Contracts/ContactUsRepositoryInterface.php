<?php

namespace HMsoft\Cms\Repositories\Contracts;

use HMsoft\Cms\Models\ContactUs\ContactUs;
use HMsoft\Cms\Repositories\Contracts\BaseRepositoryInterface;

interface ContactUsRepositoryInterface extends BaseRepositoryInterface
{
    public function destroyAll(array $ids): bool;

    /**
     * Send a reply to a specific message and store it.
     *
     * @param array $data The validated data from the request (e.g., ['reply_message' => '...'])
     * @param ContactUs $message The original message to reply to.
     * @return ContactUs The newly created reply message.
     */
    public function replyToMessage(array $data, ContactUs $message): ContactUs;
}
