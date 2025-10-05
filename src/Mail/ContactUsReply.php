<?php

namespace HMsoft\Cms\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactUsReply extends Mailable
{
    use Queueable, SerializesModels;

    public $replyMessage;
    public $originalSubject;
    public $greeting;
    public $thanks;

    /**
     * Create a new message instance.
     *
     * @param string $replyMessage
     * @param string $originalSubject
     */
    public function __construct(string $replyMessage, string $originalSubject)
    {
        $this->replyMessage = $replyMessage;
        $this->originalSubject = $originalSubject;
        $this->greeting = trans('contact.messages.greeting');
        $this->thanks = trans('contact.messages.thanks');
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        // FIX: Use translated subject prefix
        $subjectPrefix = trans('contact.messages.subject_prefix');
        $newSubject = str_starts_with($this->originalSubject, $subjectPrefix)
            ? $this->originalSubject
            : $subjectPrefix . ' ' . $this->originalSubject;

        return new Envelope(
            subject: $newSubject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.contact.reply', // We will create this Blade view next
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
