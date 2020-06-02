<?php

namespace App\Listeners;

use App\Notifications\EmailVerified;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class SendEmailVerifiedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        /** @var \App\User */
        $user = $event->user;

        if ($user instanceof MustVerifyEmail && $user->hasVerifiedEmail()) {
            $user->notify(new EmailVerified);
        }
    }
}
