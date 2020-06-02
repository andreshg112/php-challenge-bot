<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomeTest extends TestCase
{
    public function test()
    {
        $response = $this->get('/');

        $response
            ->assertStatus(200)
            ->assertSeeInOrder([
                config('app.name'),
                config('app.messages.welcome')
            ]);
    }
}
