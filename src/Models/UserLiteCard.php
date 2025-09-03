<?php

namespace Shakewell\Litecard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLiteCard extends Model
{
    protected $table = 'user_lite_cards';

    protected $fillable = [
        'user_id',
        'email',
        'card_id',
        'template_id',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the lite card.
     */
    public function user(): BelongsTo
    {
        $userModel = config('litecard.user_model', \App\Models\User::class);
        return $this->belongsTo($userModel);
    }

    /**
     * Scope for active cards.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for cards by email.
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope for cards by user ID.
     */
    public function scopeByUserId($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}