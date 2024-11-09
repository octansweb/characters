<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DeepLinkedVerifyEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Generate the verification URL with deep link scheme
        $verificationUrl = $this->deepLinkVerificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->line('Click the button below to verify your email address.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.');

    }

    protected function deepLinkVerificationUrl($notifiable)
    {
        // Generate a signed URL to your custom verification route
        return URL::temporarySignedRoute(
            'verification.verify-app', // A custom route
            now()->addMinutes(60),
            ['id' => $notifiable->getKey()]
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
