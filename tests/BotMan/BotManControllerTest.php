<?php

namespace Tests\BotMan;

use Tests\TestCase;

class BotManControllerTest extends TestCase
{
    public function testHelp()
    {
        $this->bot
            ->receives('Help')
            ->assertReply(config('app.messages.help'));
    }

    public function testHi()
    {
        $this->bot
            ->receives('Hi')
            ->assertReply(config('app.messages.hi'));
    }
}
