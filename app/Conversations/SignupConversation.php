<?php

namespace App\Conversations;

use App\User;
use Validator;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use App\Services\Amdoren\Currency;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;

class SignupConversation extends Conversation
{
    /** @var string */
    protected $currency = null;

    /** @var string */
    protected $email = null;

    /** @var string */
    protected $name = null;

    public function askCurrency()
    {
        $this->ask('What is your currency code?', function (Answer $answer) {
            $currency = mb_strtoupper(trim($answer->getText()));

            $currencies = Currency::list();

            $validator = Validator::make([
                'currency' => $currency,
            ], [
                'currency' => Rule::in($currencies->keys()),
            ]);

            if ($validator->fails()) {
                $message = $validator->errors()->first();

                $message .= ' Please, try again!';

                $this->say($message);

                $this->askCurrency();

                return;
            }

            $this->currency = $currency;

            $this->say(
                "So, your default currency is {$currencies->get($currency)}."
            );

            $this->askEmail();
        });
    }

    public function askEmail()
    {
        $this->ask('What is your email?', function (Answer $answer) {
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
                    'name'     => $this->name,
                    'email'    => $this->email,
                    'currency' => $this->currency,
                ]);
            } catch (\Throwable $th) {
                report($th);

                $this->say(config('app.messages.error'));

                return;
            }

            $user->sendEmailVerificationNotification();

            $this->say(
                "{$this->name}, you will receive a verification email."
                    . ' Open it and click on "Verify", then you can type'
                    . ' "login" here. Do not forget to check Spam.'
            );
        });
    }

    public function askName()
    {
        $this->ask('What is your name?', function (Answer $answer) {
            $this->name = ucwords(trim($answer->getText()));

            $this->say("Nice to meet you, {$this->name}!");

            $this->askCurrency();
        });
    }

    public function run()
    {
        $this->askName();
    }
}
