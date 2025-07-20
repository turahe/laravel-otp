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
            'yopmail.com'
        ]]);
    }

    #[DataProvider('validPhoneNumbersProvider')]
    public function testValidationNumberWithValidNumbers($number, $expected)
    {
        $result = validation_number($number);
        $this->assertEquals($expected, $result);
    }

    #[DataProvider('invalidPhoneNumbersProvider')]
    public function testValidationNumberWithInvalidNumbers($number, $expected, $expectException = false, $exceptionType = null)
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
    public function testFormatPhone($input, $expected)
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
    public function testFormatWhatsapp($input, $expected)
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
    public function testGetEmailProvider($email, $expected)
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
    public function testValidateEmailWithValidEmails($email)
    {
        $result = validate_email($email);
        $this->assertTrue($result);
    }

    #[DataProvider('invalidEmailProvider')]
    public function testValidateEmailWithDisposableEmails($email)
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

    public function testValidateEmailWithEmptyString()
    {
        $result = validate_email('');
        $this->assertTrue($result); // Empty string is not in disposable list, so it returns true
    }

    public function testValidateEmailWithInvalidFormat()
    {
        $result = validate_email('invalid-email');
        $this->assertTrue($result); // Invalid format is not in disposable list, so it returns true
    }

    public function testValidationNumberWithNull()
    {
        $this->expectException(\TypeError::class);
        validation_number(null);
    }

    public function testValidateEmailWithNull()
    {
        $this->expectException(\TypeError::class);
        validate_email(null);
    }

    public function testFormatPhoneWithInvalidNumber()
    {
        $this->expectException(NumberParseException::class);
        format_phone('invalid');
    }

    public function testFormatWhatsappWithInvalidNumber()
    {
        $this->expectException(NumberParseException::class);
        format_whatsapp('invalid');
    }

    public function testGetEmailProviderWithEmptyString()
    {
        $result = get_email_provider('');
        $this->assertEquals('', $result);
    }

    public function testGetEmailProviderWithNoAtSign()
    {
        $result = get_email_provider('invalid-email');
        $this->assertEquals('invalid-email', $result);
    }

    public function testGetEmailProviderWithMultipleAtSigns()
    {
        $result = get_email_provider('test@domain@example.com');
        $this->assertEquals('example.com', $result);
    }

    public function testValidationNumberWithDifferentLocales()
    {
        // Test with US number
        config(['app.locale' => 'US']);
        $result = validation_number('+1234567890');
        $this->assertIsBool($result);
    }

    public function testFormatPhoneWithDifferentLocales()
    {
        // Test with US number
        $result = format_phone('+1234567890');
        $this->assertStringStartsWith('+1', $result);
    }

    public function testFormatWhatsappWithDifferentLocales()
    {
        // Test with US number
        $result = format_whatsapp('+1234567890');
        $this->assertStringStartsWith('1', $result);
        $this->assertStringNotContainsString('+', $result);
    }
}
