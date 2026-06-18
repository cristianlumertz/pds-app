<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Order $order
    ) {
        $this->order->loadMissing(['user', 'items.product', 'address']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pedido #{$this->order->id} confirmado — ShopLaravel",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-confirmed',
        );
    }
}
