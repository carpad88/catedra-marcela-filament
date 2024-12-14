<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Notifications\WelcomeEmail;
use Illuminate\Database\Eloquent\Model;

class SendWelcomeEmail
{
    public function handle(User|Model $record): void
    {
        $notification = new WelcomeEmail($record);
        $record->notify($notification);
    }
}
