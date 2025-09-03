<?php

namespace Shakewell\Litecard\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Shakewell\Litecard\Card;
use Shakewell\Litecard\LiteCardException;

class CardController extends Controller
{
    /**
     * Handle LiteCard login.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        // You'll need to implement user authentication logic here
        // This is a basic example - customize based on your User model
        $userClass = config('litecard.user_model', \App\Models\User::class);
        $user = $userClass::where('email', $validatedData['email'])->first();

        if ($user && Hash::check($validatedData['password'], $user->password)) {
            $this->logActivity('lite_card_login_successful', $user->id, [
                'email' => $validatedData['email'],
                'login_method' => 'lite_card',
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $user->createToken('litecard-auth')->plainTextToken,
                ],
            ]);
        }

        $this->logActivity('lite_card_login_failed', $user?->id, [
            'email' => $validatedData['email'],
            'login_method' => 'lite_card',
            'user_exists' => $user !== null,
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }

    /**
     * Validate authentication token.
     */
    public function validateToken(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Valid token',
        ]);
    }

    /**
     * Create a new LiteCard.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|string',
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'member_number' => 'nullable|string',
            'expiry' => 'nullable|date',
            'email_invitation' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        try {
            $this->logActivity('lite_card_creation_attempted', auth()->id(), [
                'email' => $validatedData['email'],
                'template_id' => $validatedData['template_id'],
            ]);

            $data = $this->prepareCardData($validatedData);
            $card = new Card();
            $response = $card->create($data);

            if ($response->failed()) {
                $this->logActivity('lite_card_creation_failed', auth()->id(), [
                    'email' => $validatedData['email'],
                    'template_id' => $validatedData['template_id'],
                    'error_response' => $response->json(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Card creation failed',
                    'errors' => $response->json(),
                ], 422);
            }

            $responseData = $response->json();
            $this->recordUserLiteCard($validatedData['email'], $responseData['cardId']);

            $this->logActivity('lite_card_created', auth()->id(), [
                'email' => $validatedData['email'],
                'template_id' => $validatedData['template_id'],
                'card_id' => $responseData['cardId'],
            ]);

            return response()->json([
                'success' => true,
                'data' => $responseData,
            ], 201);

        } catch (LiteCardException $e) {
            return response()->json([
                'success' => false,
                'message' => 'LiteCard error: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update card status.
     */
    public function updateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'required|string',
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        try {
            $card = new Card();
            $response = $card->status($validatedData['card_id'], $validatedData['status']);

            if ($response->successful()) {
                $this->logActivity('lite_card_status_updated', auth()->id(), [
                    'card_id' => $validatedData['card_id'],
                    'status' => $validatedData['status'],
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Card status updated successfully',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to update card status',
                'error' => $response->body(),
            ], 422);

        } catch (LiteCardException $e) {
            return response()->json([
                'success' => false,
                'message' => 'LiteCard error: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get card by ID.
     */
    public function show(string $cardId): JsonResponse
    {
        try {
            $card = new Card();
            $response = $card->get($cardId);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Card not found',
            ], 404);

        } catch (LiteCardException $e) {
            return response()->json([
                'success' => false,
                'message' => 'LiteCard error: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Prepare card data for API.
     */
    protected function prepareCardData(array $validatedData): array
    {
        return [
            'templateId' => $validatedData['template_id'],
            'cardPayload' => [
                'firstName' => $validatedData['first_name'],
                'lastName' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'memberNumber' => $validatedData['member_number'] ?? '',
                'expiry' => $validatedData['expiry'] ?? now()->addYear()->format('Y-m-d'),
            ],
            'options' => [
                'emailInvitationEnabled' => $validatedData['email_invitation'] ?? true,
            ]
        ];
    }

    /**
     * Record user lite card relationship.
     */
    protected function recordUserLiteCard(string $email, string $cardId): void
    {
        if (class_exists(\App\Models\UserLiteCard::class)) {
            \App\Models\UserLiteCard::updateOrCreate(
                ['email' => $email],
                ['card_id' => $cardId]
            );
        }
    }

    /**
     * Log activity if logging is enabled.
     */
    protected function logActivity(string $event, ?int $userId, array $data): void
    {
        if (config('litecard.logging.enabled', true)) {
            Log::channel(config('litecard.logging.channel', 'stack'))
                ->info("[LiteCard] {$event}", array_merge($data, ['user_id' => $userId]));
        }
    }
}