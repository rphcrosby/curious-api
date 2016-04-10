<?php

use App\User;

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Authenticates the user into the application
     *
     * @param App\User $user
     * @param string $password
     * @return void
     */
    public function authenticate(User $user, $password = 'password')
    {
        $apiId = str_random(40);
        $apiSecret = str_random(40);

        // Store the oauth client in the DB
        DB::table('oauth_clients')->insert([
            'id' => $apiId,
            'secret' => $apiSecret,
            'name' => 'testing'
        ]);

        // Authenticate the user
        $response = $this->json('POST', '/authentication/user', [
            'grant_type' => 'password',
            'client_id' => $apiId,
            'client_secret' => $apiSecret,
            'username' => $user->username,
            'password' => $password
        ], [
            'Accept' => 'application/vnd.curious.v1+json'
        ])->decodeResponseJson();

        return $response['access_token'];
    }

    public function basicAuthentication()
    {
        $apiId = str_random(40);
        $apiSecret = str_random(40);

        // Store the oauth client in the DB
        DB::table('oauth_clients')->insert([
            'id' => $apiId,
            'secret' => $apiSecret,
            'name' => 'testing'
        ]);

        // Authenticate the client
        $response = $this->json('POST', '/authentication/client', [
            'grant_type' => 'client_credentials',
            'client_id' => $apiId,
            'client_secret' => $apiSecret
        ], [
            'Accept' => 'application/vnd.curious.v1+json'
        ])->decodeResponseJson();

        return $response['access_token'];
    }

    /**
     * Method for calling API methods
     *
     *
     */
    public function api($method, $uri, $params = [], $token = null)
    {
        $headers = [
            'Accept' => 'application/vnd.curious.v1+json'
        ];

        if ($token) {
            $headers['Authorization'] = "Bearer {$token}";
        } else {
            $headers['Authorization'] = "Bearer " . $this->basicAuthentication();
        }

        return parent::json($method, $uri, $params, $headers);
    }
}
