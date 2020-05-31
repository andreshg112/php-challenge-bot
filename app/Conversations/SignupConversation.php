<?php

namespace App\Conversations;

use App\User;
use Validator;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;

class SignupConversation extends Conversation
{
    /** @var string */
    protected $name = null;

    /** @var string */
    protected $email = null;

    public function askName()
    {
        $this->ask('What is your name?', function (Answer $answer) {
            $this->name = ucwords(trim($answer->getText()));

            $this->say("Nice to meet you, {$this->name}!");

            $this->askEmail();
        });
    }

    public function askEmail()
    {
        $this->ask(
            'What is your email?',
            function (Answer $answer) {
                $this->email = mb_strtolower(trim($answer->getText()));

                $validator = Validator::make([
                    'email' => $this->email,
                ], [
                    'email' => ['email', Rule::unique('users')],
                ]);

                if ($validator->fails()) {
                    $message = $validator->errors()->first();

                    if (Arr::has($validator->failed(), 'email.Unique')) {
                        $message .= ' If this is your email, type "login".';

                        $this->say($message);

                        return;
                    }

                    $message .= ' Please, try again.';

                    $this->say($message);

                    $this->askEmail();

                    return;
                }

                try {
                    $user = User::create([
                        'name'  => $this->name,
                        'email' => $this->email,
                    ]);
                } catch (\Throwable $th) {
                    report($th);

                    $this->say(config('app.error_message'));

                    return;
                }

                $user->sendEmailVerificationNotification();

                $this->say(
                    "{$this->name}, you will receive a verification email."
                        . ' Open it and click on "Verify", then you can type'
                        . ' "login" here. Do not forget to check Spam.'
                );
            }
        );
    }

    public function run()
    {
        $this->askName();
    }
}
