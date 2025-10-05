<?php

namespace HMsoft\Cms\Repositories\Eloquent;

use HMsoft\Cms\Mail\ContactUsReply;
use HMsoft\Cms\Models\ContactUs\ContactUs;
use HMsoft\Cms\Repositories\Contracts\ContactUsRepositoryInterface;
use HMsoft\Cms\Traits\General\FileManagerTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactUsRepository implements ContactUsRepositoryInterface
{

    use FileManagerTrait;

    public function __construct(
        private readonly ContactUs $model,

    ) {}


    public function store(array $data): Model
    {
        // Handle multiple file uploads if present
        if (isset($data['file-upload']) && is_array($data['file-upload'])) {
            $uploadedFileNames = [];
            foreach ($data['file-upload'] as $file) {
                $uploadedFileNames[] = $this->upload('contact_us_files/', $file);
            }
            // Store the uploaded file names as JSON or comma-separated string
            $data['file_uploads'] = json_encode($uploadedFileNames);
            unset($data['file-upload']);
        }

        $contact = $this->model->create($data);
        return $contact;
    }

    public function show(Model $model): Model
    {
        return $model;
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        $model->refresh();
        return  $model;
    }

    public function delete(Model $model): bool
    {
        $model->delete();
        return true;
    }

    public function destroy(Model $model): bool
    {
        $model->forceDelete();
        return true;
    }

    public function destroyAll(array $ids): bool
    {
        $this->model->whereIn('id', $ids)->delete();
        return true;
    }

    /**
     * Send a reply to a specific message and store it.
     *
     * @param array $data The validated data, expecting ['reply_message' => '...']
     * @param ContactUs $message The original message model instance.
     * @return ContactUs The newly created reply message model instance.
     * @throws \Exception If the email fails to send.
     */
    public function replyToMessage(array $data, ContactUs $message): ContactUs
    {
        $replyText = $data['reply_message'];
        $adminUser = Auth::user();

        Mail::to($message->email)->send(
            new ContactUsReply($replyText, $message->subject)
        );

        // FIX: Use translated subject prefix for the stored message as well
        $subjectPrefix = trans('contact.messages.subject_prefix');
        $newSubject = str_starts_with($message->subject, $subjectPrefix)
            ? $message->subject
            : $subjectPrefix . ' ' . $message->subject;

        $newReplyMessage = ContactUs::create([
            'name' => $adminUser->name ?? 'Support Team',
            'email' => $message->email,
            'mobile' => $message->mobile,
            'subject' => $newSubject,
            'message' => $replyText,
            'status' => 'read',
            'is_starred' => false,
        ]);

        return $newReplyMessage;
    }
}
