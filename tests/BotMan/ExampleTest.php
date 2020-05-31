<?php

namespace Tests\BotMan;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function testHelp()
    {
        $this->bot
            ->receives('Help')
            ->assertReply(
                'You can type the next commands:'
                    . ' 1) "convert X USD to COP"'
                    . ' to convert X dollars to Colombian pesos. You can use almost'
                    . ' any currency code'
                    . ' | 2) "signup" to register your information.'
                    . ' | 3) "login" to enter and start registering transactions.'
                    . ' | 4) "deposit" to put money in your account.'
                    . ' | 5) "withdraw" to extract money from your account.'
                    . ' | 6) "balance" to see your current account balance.'
            );
    }

    public function testHi()
    {
        $appName = config('app.name');

        $this->bot
            ->receives('Hi')
            ->assertReply(
                "Hello! My name is {$appName}. I can help you with some monetary"
                    . ' operations. Type "help" in any moment and I will show you'
                    . ' what I can do.'
            );
    }

    /**
     * A conversation test example.
     *
     * @return void
     */
    public function testConversationBasicTest()
    {
        $quotes = [
            'When there is no desire, all things are at peace. - Laozi',
            'Simplicity is the ultimate sophistication. - Leonardo da Vinci',
            'Simplicity is the essence of happiness. - Cedric Bledsoe',
            'Smile, breathe, and go slowly. - Thich Nhat Hanh',
            'Simplicity is an acquired taste. - Katharine Gerould',
            'Well begun is half done. - Aristotle',
            'He who is contented is rich. - Laozi',
            'Very little is needed to make a happy life. - Marcus Antoninus',
            'It is quality rather than quantity that matters. - Lucius Annaeus Seneca',
            'Genius is one percent inspiration and ninety-nine percent perspiration. - Thomas Edison',
            'Computer science is no more about computers than astronomy is about telescopes. - Edsger Dijkstra',
        ];

        $this->bot
            ->receives('Start Conversation')
            ->assertQuestion('Huh - you woke me up. What do you need?')
            ->receivesInteractiveMessage('quote')
            ->assertReplyIn($quotes);
    }
}
