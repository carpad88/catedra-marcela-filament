<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Notifications\InvitationEmail;
use Illuminate\Database\Eloquent\Model;

class SendWelcomeEmail
{
    public function handle(User|Model $record): void
    {
        $notification = new InvitationEmail($record);
        $record->notify($notification);
    }
}
