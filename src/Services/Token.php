<?php

namespace Turahe\Otp\Services;

use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Turahe\Otp\Contracts\TokenInterface;
use Turahe\Otp\Models\OtpToken as Model;
use Turahe\Otp\Notifications\TokenNotification;

class Token implements TokenInterface
{
    /**
     * The attributes of the token.
     *
     * @var array
     */
    public array $attributes = [
        'identity' => null,
        'token' => null,
        'expired' => null,
        'created_at' => null,
        'updated_at' => null,
    ];

    /**
     * Token constructor.
     *
     * @param string|int $identity
     * @param string $token
     * @param null|int $expiryTime
     * @param null|Carbon $createdAt
     * @param null|Carbon $updatedAt
     */
    public function __construct(
        string|int $identity,
        string $token,
        ?int $expiryTime = null,
        ?Carbon $createdAt = null,
        ?Carbon $updatedAt = null
    ) {
        $now = $this->getNow();

        $this->attributes['identity'] = $identity;
        $this->attributes['token'] = $token;
        $this->attributes['created_at'] = $createdAt ?: $now;
        $this->attributes['updated_at'] = $updatedAt ?: $now;
        $this->attributes['expired'] = null === $expiryTime ? $this->getDefaultExpiryTime() : $expiryTime;
    }


    /**
     * @param string|int $identity
     * @param int $expiresAt
     * @return Token
     */
    public static function generate(string|int $identity, int $expiresAt = 10): self
    {
        Model::where('identity', $identity)->delete();

        $token = mt_rand(100000, 999999);

        return Model::create([
            'identity' => $identity,
            'token' => $token,
            'expired' => Carbon::now()->addMinutes($expiresAt)
        ]);
    }

    /**
     * @param string $identity
     * @param string $token
     * @return bool
     */
    public function validate(string $identity, string $token): bool
    {
        $otp = Model::where(['identity' => $identity, 'token' => $token])
            ->where('expired', '>', Carbon::now())
            ->first();

        if ($otp) {
            return $otp->delete();
        }

        return false;
    }

    public function identity()
    {
        return $this->attributes['identity'];
    }

    public function token(): string
    {
        return $this->attributes['token'];
    }

    public function createdAt(): Carbon
    {
        return clone $this->attributes['created_at'];
    }

    public function updatedAt(): Carbon
    {
        return clone $this->attributes['updated_at'];
    }

    public function expiryTime(): int
    {
        return $this->attributes['expiry_time'];
    }

    public function expiresAt(): Carbon
    {
        return (clone $this->createdAt())->addSeconds($this->expiryTime());
    }

    public function timeLeft(): int
    {
        return $this->getNow()->diffInSeconds($this->expiresAt(), false);
    }

    public function expired(): bool
    {
        return $this->timeLeft() <= 0;
    }

    public function revoke(): void
    {
        $this->invalidate();
    }

    public function invalidate(): void
    {
        $this->attributes['expiry_time'] = 0;

        $this->persist();
    }

    public function refresh(): bool
    {
        return $this->extend(
            $this->getNow()->diffInSeconds($this->updatedAt())
        );
    }

    public function extend(?int $seconds = null): bool
    {
        $seconds = null === $seconds ? $this->getDefaultExpiryTime() : $seconds;

        $this->attributes['expiry_time'] += $seconds;

        return $this->persist();
    }

    public function toNotification(): Notification
    {
        return new TokenNotification($this);
    }

    public static function retrieveByAttributes(array $attributes): ?self
    {
        // TODO: Implement retrieveByAttributes() method.
    }

    /**
     * Persist the token in the storage.
     *
     * @return bool
     */
    protected function persist(): bool
    {
        $this->attributes['updated_at'] = $this->getNow();

        $attributes = $this->attributes;
        $attributes['created_at'] = $attributes['created_at']->toDateTimeString();
        $attributes['updated_at'] = $attributes['updated_at']->toDateTimeString();

        if (array_key_exists('token', $attributes)) {
            unset($attributes['token']);
        }

        try {
            DB::beginTransaction();

            DB::table(self::getTable())->updateOrInsert([
                'identity' => $this->identity(),
                'token'      => $this->cipherText(),
            ], $attributes);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            throw new \RuntimeException(
                'Something went wrong while saving the access token.',
                0,
                $e
            );
        }

        return true;
    }

    /**
     * Get the date time at the moment.
     *
     * @return Carbon
     */
    private function getNow(): Carbon
    {
        return Carbon::now();
    }

    /**
     * Get the name of the table token will be persisted.
     *
     * @return string
     */
    private static function getTable(): string
    {
        return config('otp.table');
    }

    /**
     * Get the default expiry time in seconds.
     *
     * @return int
     */
    private function getDefaultExpiryTime(): int
    {
        return config('otp.expires') * 60;
    }
}
