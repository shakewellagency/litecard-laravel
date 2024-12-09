<?php

namespace Shakewell\Litecard;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Authenticator
{
    protected Response $response;

    protected ?string $token = null;

    public function token(): string
    {
        return (new static())
            ->integrate()
            ->getToken();
    }

    public function integrate(): static
    {
        $this->response = Http::timeout(15)
            ->post(config('litecard.baseurl') . '/api/v1/token', [
                'username' => config('litecard.username'),
                'password' => config('litecard.password'),
            ]);

        if ($this->response->successful()) {
            $this->token = $this->response->json()['access_token'];
        }

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
