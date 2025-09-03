<?php

namespace Shakewell\Litecard;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Authenticator
{
    protected Response $response;

    protected ?string $token = null;

    /**
     * Get cached authentication token (static method).
     */
    public static function token(): string
    {
        return Cache::remember('lite_card_token', 86300, function () {
            return (new static)
                ->integrate()
                ->getToken();
        });
    }

    /**
     * Instance method for backward compatibility.
     */
    public function getAccessToken(): string
    {
        return self::token();
    }

    /**
     * Authenticate with LiteCard API.
     */
    public function integrate(): static
    {
        $this->response = Http::timeout(15)
            ->post(config('litecard.base_url') . '/api/v1/token', [
                'username' => config('litecard.username'),
                'password' => config('litecard.password'),
            ]);

        if ($this->response->successful()) {
            $this->token = $this->response->json()['access_token'];
        }

        return $this;
    }

    /**
     * Get authentication response.
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Get authentication token.
     */
    public function getToken(): ?string
    {
        if (!$this->token) {
            throw new LiteCardException('Authentication failed. Token not available.');
        }

        return $this->token;
    }
}
