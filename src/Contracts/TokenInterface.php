<?php

namespace Turahe\Otp\Contracts;

use Carbon\Carbon;
use Illuminate\Notifications\Notification;

interface TokenInterface
{
    /**
     * Get the unique identity of the token.
     * who owns the token.
     *
     * @return mixed
     */
    public function identity();

    /**
     * Get the token as cipher text.
     */
    public function token(): string;

    /**
     * Get the date token created.
     */
    public function createdAt(): Carbon;

    /**
     * Get the last update date of the token.
     */
    public function updatedAt(): Carbon;

    /**
     * Get the expiry time of the token in seconds.
     */
    public function expiryTime(): int;

    /**
     * Get the date time the token will expire.
     */
    public function expiresAt(): Carbon;

    /**
     * Get the validity time left for the token.
     */
    public function timeLeft(): int;

    /**
     * Determine if the token is expired or not.
     */
    public function expired(): bool;

    /**
     * Alias for invalidate.
     */
    public function revoke(): void;

    /**
     * Invalidate the token.
     */
    public function invalidate(): void;

    /**
     * Refresh the token.
     */
    public function refresh(): bool;

    /**
     * Extend the validity of the token.
     */
    public function extend(?int $seconds = null): bool;

    /**
     * Convert the token to a token notification.
     */
    public function toNotification(): Notification;

    /**
     * Create a new token.
     */
    public static function generate(
        string|int $identity,
        int $expiresAt,
    ): self;

    /**
     * Retrieve a token by the given attributes from the storage.
     */
    public static function retrieveByAttributes(array $attributes): ?self;
}
