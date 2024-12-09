<?php

namespace Shakewell\Litecard;

use Shakewell\Litecard\Enums\CardStatusEnum;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Card
{
    public function create(string $templateId, string $firstName, string $lastName, string $email, string $expiry, bool $emailInvitation): Response
    {
        $token = (new Authenticator())->token();

        return Http::withToken($token)->post(
            config('litecard.baseurl') . '/api/v1/card',
            [
                'templateId' => $templateId,
                'cardPayload' => [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'email' => $email,
                    'expiry' => $expiry,
                ],
                'options' => [
                    'emailInvitationEnabled' => $emailInvitation,
                ]
            ]);
    }

    public function status(string $cardId, string $status): bool
    {
        if (!in_array($status, CardStatusEnum::values())) {
            throw new LiteCardException('invalid card status');
        }

        $token = (new Authenticator())->token();

        $response = Http::withToken($token)->post(
            config('litecard.baseurl') . '/api/v1/card/status',
            [
                'cardId' => $cardId,
                'status' => $status
            ]
        );

        if ($response->successful()) {
            return true;
        }

        return false;
    }

    public function get(string $cardId): array
    {
        $token = (new Authenticator())->token();

        $response = Http::withToken($token)->get(
            config('litecard.baseurl') . '/api/v1/card/' . $cardId
        );

        if ($response->successful()) {
            return $response->json();
        }

        throw new LiteCardException('Integration failed');
    }
}
