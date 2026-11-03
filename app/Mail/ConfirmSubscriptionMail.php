<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ConfirmSubscriptionMail extends Mailable
{
    public function __construct(public string $url, public string $siteName, public string $locale_) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('site.newsletter_confirm_subject', ['name' => $this->siteName], $this->locale_));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.confirm-subscription');
    }
}
