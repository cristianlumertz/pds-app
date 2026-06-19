<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterPromotion extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $campaignTitle,
        public string $campaignBody,
        public string $ctaText,
        public string $ctaUrl,
        public ?string $couponCode = null,
        public ?string $couponDescription = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaignTitle.' — ShopLaravel',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter-promotion',
        );
    }
}
