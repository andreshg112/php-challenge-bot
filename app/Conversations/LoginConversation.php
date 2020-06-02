<?php

namespace App\Conversations;

use Auth;
use App\User;
use Illuminate\Support\Str;
use App\Notifications\LoginCode;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;

class LoginConversation extends Conversation
{
    /** @var string */
    protected $code = null;

    /** @var \App\User */
    protected $user = null;

    public function askCode()
    {
        $this->ask(
            'Please, type the six characters code you received in your email.'
                . ' Do not forget to check Spam.',
            function (Answer $answer) {
                $code = $answer->getText();

                if ($code !== $this->code) {
                    $this->say('Wrong code!');

                    return;
                }

                $this->bot->userStorage()->save($this->user->toArray());

                $message = config('app.messages.you_are_in');

                $this->say("{$this->user->name}, {$message}");
            }
        );
    }

    public function askEmail()
    {
        $this->ask(
            'Please, type your email.',
            function (Answer $answer) {
                $email = $answer->getText();

                /** @var \App\User */
                $this->user = User::whereEmail($email)->first();

                if (is_null($this->user)) {
                    $this->say(
                        "There is no user with the email {$email}."
                            . ' Type "signup" if you want to register.'
                    );

                    return;
                }

                if (!$this->user->hasVerifiedEmail()) {
                    $this->user->sendEmailVerificationNotification();

                    $this->say(
                        'The email has not been verified. A verification link'
                            . " was sent to {$email}. Click on it and then"
                            . ' type "login" here. Do not forget to check Spam.'
                    );

                    return;
                }

                $this->code = Str::random(6);

                $this->user->notify(new LoginCode($this->code));

                $this->askCode();
            }
        );
    }

    public function run()
    {
        if (Auth::check()) {
            /** @var \App\User */
            $user = Auth::user();

            $message = config('app.messages.you_are_in')
                . ' If you want to start with a new account, type "logout".';

            $this->say("{$user->name}, {$message}");

            return;
        }

        $this->askEmail();
    }
}
