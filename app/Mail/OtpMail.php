<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $firstName;
    public $type;

    /**
     * Create a new message instance.
     *
     * @param int $code
     * @param string|null $firstName
     * @param string $type  // 'registration', 'password_reset', 'login', etc.
     */
    public function __construct($code, $firstName = null, $type = 'registration')
    {
        $this->code = $code;
        $this->firstName = $firstName;
        $this->type = $type;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match ($this->type) {
            'password_reset' => 'Password Reset OTP - Fitcoin',
            'login'          => 'Login OTP - Fitcoin',
            default          => 'Your OTP Verification Code - Fitcoin',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'authentication.otp',
            with: [
                'code'      => $this->code,
                'firstName' => $this->firstName,
                'type'      => $this->type,
            ]
        );
    }
}