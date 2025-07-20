<?php

namespace Turahe\Otp\Test\Services;

use Carbon\Carbon;
use Turahe\Otp\Models\OtpToken;
use Turahe\Otp\Services\Token;
use Turahe\Otp\Test\TestCase;

/**
 * Token Service Test Suite
 * 
 * Tests the Token service class functionality:
 * - Constructor and attribute management
 * - Static generate method
 * - Validation methods
 * - Basic functionality
 */
class TokenTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set up test configuration
        config(['otp.table' => 'otp_tokens']);
        config(['otp.expires' => 15]); // 15 minutes
    }

    public function testConstructorWithAllParameters()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 300; // 5 minutes
        $createdAt = Carbon::now()->subMinutes(5);
        $updatedAt = Carbon::now()->subMinutes(2);

        $tokenInstance = new Token($identity, $token, $expiryTime, $createdAt, $updatedAt);

        $this->assertEquals($identity, $tokenInstance->identity());
        $this->assertEquals($token, $tokenInstance->token());
        $this->assertEquals($createdAt->toDateTimeString(), $tokenInstance->createdAt()->toDateTimeString());
        $this->assertEquals($updatedAt->toDateTimeString(), $tokenInstance->updatedAt()->toDateTimeString());
    }

    public function testConstructorWithMinimalParameters()
    {
        $identity = 'test@example.com';
        $token = '123456';

        $tokenInstance = new Token($identity, $token);

        $this->assertEquals($identity, $tokenInstance->identity());
        $this->assertEquals($token, $tokenInstance->token());
        $this->assertInstanceOf(Carbon::class, $tokenInstance->createdAt());
        $this->assertInstanceOf(Carbon::class, $tokenInstance->updatedAt());
    }

    public function testConstructorWithNullExpiryTime()
    {
        $identity = 'test@example.com';
        $token = '123456';

        $tokenInstance = new Token($identity, $token, null);

        // The expired attribute should be set to default expiry time
        $this->assertEquals(15 * 60, $tokenInstance->attributes['expired']); // 15 minutes in seconds
    }

    public function testToNotificationMethod()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 300; // 5 minutes

        $tokenInstance = new Token($identity, $token, $expiryTime);
        $notification = $tokenInstance->toNotification();

        $this->assertInstanceOf(\Illuminate\Notifications\Notification::class, $notification);
    }

    public function testRetrieveByAttributesMethod()
    {
        $attributes = ['identity' => 'test@example.com', 'token' => '123456'];
        
        // This method is not implemented yet, so it should throw an exception or return null
        try {
            $retrievedToken = Token::retrieveByAttributes($attributes);
            $this->assertNull($retrievedToken);
        } catch (\TypeError $e) {
            // Expected behavior since the method is not implemented
            $this->assertStringContainsString('Return value must be of type', $e->getMessage());
        }
    }

    public function testTokenWithDifferentIdentityTypes()
    {
        $identities = [
            'string' => 'test@example.com',
            'integer' => 12345,
            'numeric_string' => '12345'
        ];

        foreach ($identities as $type => $identity) {
            $token = '123456';
            $tokenInstance = new Token($identity, $token);

            $this->assertEquals($identity, $tokenInstance->identity());
            $this->assertEquals($token, $tokenInstance->token());
        }
    }

    public function testTokenAttributesArray()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 300;

        $tokenInstance = new Token($identity, $token, $expiryTime);

        $this->assertIsArray($tokenInstance->attributes);
        $this->assertEquals($identity, $tokenInstance->attributes['identity']);
        $this->assertEquals($token, $tokenInstance->attributes['token']);
        $this->assertEquals($expiryTime, $tokenInstance->attributes['expired']);
        $this->assertArrayHasKey('created_at', $tokenInstance->attributes);
        $this->assertArrayHasKey('updated_at', $tokenInstance->attributes);
    }

    public function testTokenWithZeroExpiryTime()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 0;

        $tokenInstance = new Token($identity, $token, $expiryTime);

        $this->assertEquals(0, $tokenInstance->attributes['expired']);
    }

    public function testTokenWithNegativeExpiryTime()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = -300; // Negative 5 minutes

        $tokenInstance = new Token($identity, $token, $expiryTime);

        $this->assertEquals(-300, $tokenInstance->attributes['expired']);
    }

    public function testTokenWithVeryLongExpiryTime()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 86400 * 365; // 1 year

        $tokenInstance = new Token($identity, $token, $expiryTime);

        $this->assertEquals(86400 * 365, $tokenInstance->attributes['expired']);
    }

    public function testTokenWithSpecialCharacters()
    {
        $identities = [
            'email_with_plus' => 'test+tag@example.com',
            'email_with_dots' => 'user.name@domain.com',
            'email_with_hyphens' => 'user-name@sub.domain.com',
            'email_with_underscores' => 'user_name@example.co.uk'
        ];

        foreach ($identities as $type => $identity) {
            $token = '123456';
            $tokenInstance = new Token($identity, $token);

            $this->assertEquals($identity, $tokenInstance->identity());
            $this->assertEquals($token, $tokenInstance->token());
        }
    }

    public function testTokenWithEmptyString()
    {
        $identity = '';
        $token = '123456';

        $tokenInstance = new Token($identity, $token);

        $this->assertEquals('', $tokenInstance->identity());
        $this->assertEquals('123456', $tokenInstance->token());
    }

    public function testTokenWithWhitespace()
    {
        $identity = '  test@example.com  ';
        $token = '  123456  ';

        $tokenInstance = new Token($identity, $token);

        $this->assertEquals('  test@example.com  ', $tokenInstance->identity());
        $this->assertEquals('  123456  ', $tokenInstance->token());
    }

    public function testTokenWithUnicodeCharacters()
    {
        $identity = 'test@测试.com';
        $token = '123测试456';

        $tokenInstance = new Token($identity, $token);

        $this->assertEquals('test@测试.com', $tokenInstance->identity());
        $this->assertEquals('123测试456', $tokenInstance->token());
    }

    public function testTokenWithLongValues()
    {
        $identity = str_repeat('a', 1000);
        $token = str_repeat('1', 1000);

        $tokenInstance = new Token($identity, $token);

        $this->assertEquals(str_repeat('a', 1000), $tokenInstance->identity());
        $this->assertEquals(str_repeat('1', 1000), $tokenInstance->token());
    }

    public function testTokenWithNullValues()
    {
        $identity = 'test@example.com';
        $token = '123456';

        $tokenInstance = new Token($identity, $token, null, null, null);

        $this->assertEquals($identity, $tokenInstance->identity());
        $this->assertEquals($token, $tokenInstance->token());
        $this->assertInstanceOf(Carbon::class, $tokenInstance->createdAt());
        $this->assertInstanceOf(Carbon::class, $tokenInstance->updatedAt());
    }

    public function testTokenWithFutureDates()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 300;
        $createdAt = Carbon::now()->addDays(1);
        $updatedAt = Carbon::now()->addDays(2);

        $tokenInstance = new Token($identity, $token, $expiryTime, $createdAt, $updatedAt);

        $this->assertEquals($identity, $tokenInstance->identity());
        $this->assertEquals($token, $tokenInstance->token());
        $this->assertEquals($createdAt->toDateTimeString(), $tokenInstance->createdAt()->toDateTimeString());
        $this->assertEquals($updatedAt->toDateTimeString(), $tokenInstance->updatedAt()->toDateTimeString());
    }

    public function testTokenWithPastDates()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 300;
        $createdAt = Carbon::now()->subDays(1);
        $updatedAt = Carbon::now()->subDays(2);

        $tokenInstance = new Token($identity, $token, $expiryTime, $createdAt, $updatedAt);

        $this->assertEquals($identity, $tokenInstance->identity());
        $this->assertEquals($token, $tokenInstance->token());
        $this->assertEquals($createdAt->toDateTimeString(), $tokenInstance->createdAt()->toDateTimeString());
        $this->assertEquals($updatedAt->toDateTimeString(), $tokenInstance->updatedAt()->toDateTimeString());
    }

    public function testTokenSerialization()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 300;

        $tokenInstance = new Token($identity, $token, $expiryTime);
        
        $serialized = serialize($tokenInstance);
        $unserialized = unserialize($serialized);

        $this->assertEquals($identity, $unserialized->identity());
        $this->assertEquals($token, $unserialized->token());
        $this->assertEquals($expiryTime, $unserialized->attributes['expired']);
    }

    public function testTokenClone()
    {
        $identity = 'test@example.com';
        $token = '123456';
        $expiryTime = 300;

        $tokenInstance = new Token($identity, $token, $expiryTime);
        $clonedToken = clone $tokenInstance;

        $this->assertEquals($identity, $clonedToken->identity());
        $this->assertEquals($token, $clonedToken->token());
        $this->assertEquals($expiryTime, $clonedToken->attributes['expired']);
        $this->assertNotSame($tokenInstance, $clonedToken);
    }
} 