<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use URL;

class InvitationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public User $user;

    public string $url;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->url = $this->getActivationUrl();
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
        return (new MailMessage)
            ->subject('Registro en Cátedra Marcela')
            ->markdown('mail.auth.invitation', [
                'url' => $this->url,
                'user' => $this->user,
            ]);
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

    public function getActivationUrl(): string
    {
        $token = app('auth.password.broker')->createToken($this->user);

        return URL::temporarySignedRoute(
            'account.activation',
            now()->addDays(15),
            [
                'token' => $token,
                'email' => $this->user->email,
            ]
        );
    }
}
