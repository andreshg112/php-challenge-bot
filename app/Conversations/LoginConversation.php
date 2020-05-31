<?php

namespace App\Conversations;

use App\User;
use Illuminate\Support\Str;
use App\Notifications\LoginCode;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;

class LoginConversation extends Conversation
{
    /** @var string */
    protected $code = null;

    /** @var string */
    protected $email = null;

    /** @var \App\User */
    protected $user = null;

    public function askCode()
    {
        $this->ask(
            'Please, type the six characters code you received in your email.',
            function (Answer $answer) {
                $code = $answer->getText();

                if ($code !== $this->code) {
                    $this->say('Wrong code!');

                    return;
                }

                $this->say('You are in!');
            }
        );
    }

    public function askEmail()
    {
        $this->ask(
            'Please, type your email.',
            function (Answer $answer) {
                $this->email = $answer->getText();

                /** @var \App\User */
                $this->user = User::whereEmail($this->email)->first();

                if (is_null($this->user)) {
                    $this->say(
                        "There is no user with the email: {$this->email}."
                            . ' Type "signup" if you want to register.'
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
        $this->askEmail();
    }
}
