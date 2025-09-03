<?php

namespace Shakewell\Litecard;

use Shakewell\Litecard\Enums\CardStatusEnum;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class Card
{
    /**
     * Create a new card with flexible data structure.
     */
    public function create(array $data): Response
    {
        $token = Authenticator::token();

        return Http::withToken($token)->post(
            config('litecard.base_url') . '/api/v1/card',
            $data,
        );
    }

    /**
     * Create a card with specific parameters (backward compatibility).
     */
    public function createCard(string $templateId, string $firstName, string $lastName, string $email, string $expiry, bool $emailInvitation = true): Response
    {
        return $this->create([
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

    /**
     * Update card status.
     */
    public function status(string $cardId, string $status): Response
    {
        if (!in_array($status, CardStatusEnum::values())) {
            throw new LiteCardException('Invalid card status: ' . $status);
        }

        $token = Authenticator::token();

        return Http::withToken($token)->post(
            config('litecard.base_url') . '/api/v1/card/status',
            [
                'cardId' => $cardId,
                'status' => $status,
            ]
        );
    }

    /**
     * Get card details by ID.
     */
    public function get(string $cardId): Response
    {
        $token = Authenticator::token();

        return Http::withToken($token)->get(
            config('litecard.base_url') . '/api/v1/card/' . $cardId
        );
    }

    /**
     * Get card details as array (backward compatibility).
     */
    public function getCard(string $cardId): array
    {
        $response = $this->get($cardId);

        if ($response->successful()) {
            return $response->json();
        }

        throw new LiteCardException('Failed to retrieve card: ' . $response->body());
    }
}
