<?php

namespace Shakewell\Litecard\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Shakewell\Litecard\Authenticator;
use Shakewell\Litecard\LiteCardException;

class TemplateService
{
    /**
     * Get all available templates from LiteCard API.
     */
    public function getTemplates(): Collection
    {
        try {
            $token = Authenticator::token();
            
            $response = Http::withToken($token)->get(
                config('litecard.base_url') . '/api/v1/templates'
            );

            if ($response->successful()) {
                return collect($response->json('data', []));
            }

            throw new LiteCardException('Failed to retrieve templates: ' . $response->body());

        } catch (\Exception $e) {
            throw new LiteCardException('Template service error: ' . $e->getMessage());
        }
    }

    /**
     * Get template by ID.
     */
    public function getTemplate(string $templateId): array
    {
        try {
            $token = Authenticator::token();
            
            $response = Http::withToken($token)->get(
                config('litecard.base_url') . '/api/v1/templates/' . $templateId
            );

            if ($response->successful()) {
                return $response->json();
            }

            throw new LiteCardException('Template not found: ' . $templateId);

        } catch (\Exception $e) {
            throw new LiteCardException('Template service error: ' . $e->getMessage());
        }
    }

    /**
     * Alias for getTemplates() for backward compatibility.
     */
    public function connect(): Collection
    {
        return $this->getTemplates();
    }
}