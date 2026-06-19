<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmail
{
    private string $userName = '';

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): MailMessage
    {
        $this->userName = (string) $notifiable->name;

        return $this->buildMailMessage($this->verificationUrl($notifiable));
    }

    /**
     * Get the verify email notification mail message for the given URL.
     */
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirme seu e-mail — ShopLaravel')
            ->view('emails.verify-email', [
                'url' => $url,
                'userName' => $this->userName,
            ]);
    }
}
