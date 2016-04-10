<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserAuthentication()
    {
        $user = factory(User::class)->create();
        $token = $this->authenticate($user);
        $this->assertNotNull($token);
    }
}
