<?php

namespace Turahe\Otp\Test;

use libphonenumber\NumberParseException;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Helper Functions Test Suite
 *
 * Tests all helper functions in src/Helpers.php:
 * - validation_number(): Phone number validation
 * - format_phone(): Phone number formatting to E164
 * - format_whatsapp(): Phone number formatting for WhatsApp
 * - get_email_provider(): Extract email provider domain
 * - validate_email(): Check if email is from disposable provider
 */
class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set up test configuration
        config(['app.locale' => 'id']);
        config(['disposable-email-providers' => [
            'tempmail.org',
            '10minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
            'yopmail.com',
        ]]);
    }

    #[DataProvider('validPhoneNumbersProvider')]
    public function test_validation_number_with_valid_numbers($number, $expected)
    {
        $result = validation_number($number);
        $this->assertEquals($expected, $result);
    }

    #[DataProvider('invalidPhoneNumbersProvider')]
    public function test_validation_number_with_invalid_numbers($number, $expected, $expectException = false, $exceptionType = null)
    {
        if ($expectException) {
            $this->expectException($exceptionType);
            validation_number($number);
        } else {
            $result = validation_number($number);
            $this->assertEquals($expected, $result);
        }
    }

    public static function validPhoneNumbersProvider()
    {
        return [
            'valid indonesian number' => ['+628123456789', true],
            'valid indonesian number without plus' => ['628123456789', true],
            'valid indonesian number with spaces' => ['+62 812 345 6789', true],
            'valid indonesian number with dashes' => ['+62-812-345-6789', true],
            'valid indonesian number with dots' => ['+62.812.345.6789', true],
            'valid indonesian number with parentheses' => ['+62 (812) 345-6789', true],
        ];
    }

    public static function invalidPhoneNumbersProvider()
    {
        return [
            'empty string' => ['', false, true, NumberParseException::class],
            'invalid characters' => ['abc123', false],
            'too short' => ['123', false],
            'too long' => ['123456789012345678901234567890', false, true, NumberParseException::class],
        ];
    }

    #[DataProvider('formatPhoneProvider')]
    public function test_format_phone($input, $expected)
    {
        $result = format_phone($input);
        $this->assertEquals($expected, $result);
    }

    public static function formatPhoneProvider()
    {
        return [
            'indonesian number with plus' => ['+628123456789', '+628123456789'],
            'indonesian number without plus' => ['628123456789', '+628123456789'],
            'indonesian number with spaces' => ['+62 812 345 6789', '+628123456789'],
            'indonesian number with dashes' => ['+62-812-345-6789', '+628123456789'],
            'indonesian number with dots' => ['+62.812.345.6789', '+628123456789'],
            'indonesian number with parentheses' => ['+62 (812) 345-6789', '+628123456789'],
            'indonesian number with leading zeros' => ['08123456789', '+628123456789'],
        ];
    }

    #[DataProvider('formatWhatsappProvider')]
    public function test_format_whatsapp($input, $expected)
    {
        $result = format_whatsapp($input);
        $this->assertEquals($expected, $result);
    }

    public static function formatWhatsappProvider()
    {
        return [
            'indonesian number with plus' => ['+628123456789', '628123456789'],
            'indonesian number without plus' => ['628123456789', '628123456789'],
            'indonesian number with spaces' => ['+62 812 345 6789', '628123456789'],
            'indonesian number with dashes' => ['+62-812-345-6789', '628123456789'],
            'indonesian number with dots' => ['+62.812.345.6789', '628123456789'],
            'indonesian number with parentheses' => ['+62 (812) 345-6789', '628123456789'],
            'indonesian number with leading zeros' => ['08123456789', '628123456789'],
        ];
    }

    #[DataProvider('emailProviderProvider')]
    public function test_get_email_provider($email, $expected)
    {
        $result = get_email_provider($email);
        $this->assertEquals($expected, $result);
    }

    public static function emailProviderProvider()
    {
        return [
            'gmail provider' => ['test@gmail.com', 'gmail.com'],
            'yahoo provider' => ['user@yahoo.com', 'yahoo.com'],
            'outlook provider' => ['contact@outlook.com', 'outlook.com'],
            'custom domain' => ['admin@example.com', 'example.com'],
            'subdomain' => ['info@mail.example.com', 'mail.example.com'],
            'multiple at signs' => ['test@domain@example.com', 'example.com'],
        ];
    }

    #[DataProvider('validEmailProvider')]
    public function test_validate_email_with_valid_emails($email)
    {
        $result = validate_email($email);
        $this->assertTrue($result);
    }

    #[DataProvider('invalidEmailProvider')]
    public function test_validate_email_with_disposable_emails($email)
    {
        $result = validate_email($email);
        $this->assertFalse($result);
    }

    public static function validEmailProvider()
    {
        return [
            'gmail' => ['test@gmail.com'],
            'yahoo' => ['user@yahoo.com'],
            'outlook' => ['contact@outlook.com'],
            'custom domain' => ['admin@example.com'],
            'subdomain' => ['info@mail.example.com'],
        ];
    }

    public static function invalidEmailProvider()
    {
        return [
            'tempmail' => ['test@tempmail.org'],
            '10minutemail' => ['user@10minutemail.com'],
            'guerrillamail' => ['contact@guerrillamail.com'],
            'mailinator' => ['admin@mailinator.com'],
            'yopmail' => ['info@yopmail.com'],
        ];
    }

    public function test_validate_email_with_empty_string()
    {
        $result = validate_email('');
        $this->assertTrue($result); // Empty string is not in disposable list, so it returns true
    }

    public function test_validate_email_with_invalid_format()
    {
        $result = validate_email('invalid-email');
        $this->assertTrue($result); // Invalid format is not in disposable list, so it returns true
    }

    public function test_validation_number_with_null()
    {
        $this->expectException(\TypeError::class);
        validation_number(null);
    }

    public function test_validate_email_with_null()
    {
        $this->expectException(\TypeError::class);
        validate_email(null);
    }

    public function test_format_phone_with_invalid_number()
    {
        $this->expectException(NumberParseException::class);
        format_phone('invalid');
    }

    public function test_format_whatsapp_with_invalid_number()
    {
        $this->expectException(NumberParseException::class);
        format_whatsapp('invalid');
    }

    public function test_get_email_provider_with_empty_string()
    {
        $result = get_email_provider('');
        $this->assertEquals('', $result);
    }

    public function test_get_email_provider_with_no_at_sign()
    {
        $result = get_email_provider('invalid-email');
        $this->assertEquals('invalid-email', $result);
    }

    public function test_get_email_provider_with_multiple_at_signs()
    {
        $result = get_email_provider('test@domain@example.com');
        $this->assertEquals('example.com', $result);
    }

    public function test_validation_number_with_different_locales()
    {
        // Test with US number
        config(['app.locale' => 'US']);
        $result = validation_number('+1234567890');
        $this->assertIsBool($result);
    }

    public function test_format_phone_with_different_locales()
    {
        // Test with US number
        $result = format_phone('+1234567890');
        $this->assertStringStartsWith('+1', $result);
    }

    public function test_format_whatsapp_with_different_locales()
    {
        // Test with US number
        $result = format_whatsapp('+1234567890');
        $this->assertStringStartsWith('1', $result);
        $this->assertStringNotContainsString('+', $result);
    }
}
