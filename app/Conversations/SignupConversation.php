<?php

namespace App\Conversations;

use App\User;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;

class SignupConversation extends Conversation
{
    protected $firstname;

    protected $email;

    public function askFirstname()
    {
        $this->ask('What is your firstname?', function (Answer $answer) {
            $this->firstname = $answer->getText();

            $this->say('Nice to meet you ' . $this->firstname);

            $this->askEmail();
        });
    }

    public function askEmail()
    {
        $this->ask(
            'One more thing - what is your email?',
            function (Answer $answer) {
                $this->email = $answer->getText();

                $this->say('Great - that is all we need, ' . $this->firstname);

                $user = User::create([
                    'name'  => $this->firstname,
                    'email' => $this->email,
                ]);

                $user->sendEmailVerificationNotification();
            }
        );
    }

    public function run()
    {
        $this->askFirstname();
    }
}
