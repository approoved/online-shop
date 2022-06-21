<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $user;

    /**
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $url = env('APP_URL') . '/users/me/verification?token=' . $this->user->token;

        return (new MailMessage)
                    ->greeting('Hello, ' . $this->user->first_name . " " . $this->user->last_name . "!")
                    ->line('Please verify your email by clicking the button below!')
                    ->action('Verify Email', $url)
                    ->line('Thank you for using ' . env('APP_NAME') . "!");
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }
}
