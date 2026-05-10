<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HelpCenterMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $firstName;
    public string $userEmail;
    public string $issueType;
    public string $details;

    /**
     * Create a new message instance.
     */
    public function __construct(string $firstName, string $userEmail, string $issueType, string $details)
    {
        $this->firstName = $firstName;
        $this->userEmail = $userEmail;
        $this->issueType = $issueType;
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[GovKloud Support] {$this->issueType} — from {$this->firstName}",
            replyTo: [$this->userEmail],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.help-center',
        );
    }
}
