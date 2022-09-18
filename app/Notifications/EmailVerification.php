<?php

namespace App\Notifications;

use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected User $user)
    {
    }

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        $url = config('app.url') . '/users/me/verification?token=' . $this->user->token;

        return (new MailMessage)
            ->greeting('Hello, ' . $this->user->first_name . " " . $this->user->last_name . "!")
            ->line('Please verify your email by clicking the button below!')
            ->action('Verify Email', $url)
            ->line('Thank you for using ' . config('app.name') . "!");
    }
}
