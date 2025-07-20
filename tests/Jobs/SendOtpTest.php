<?php

namespace Turahe\Otp\Test\Jobs;

use Turahe\Otp\Jobs\SendOtp;
use Turahe\Otp\Mail\SendOtp as SendOtpMail;
use Turahe\Otp\Test\TestCase;
use Illuminate\Support\Facades\Mail;

/**
 * SendOtp Job Test Suite
 * 
 * Tests the SendOtp job class functionality:
 * - Constructor parameter assignment
 * - Handle method execution
 * - Mail sending verification
 * - Error handling
 */
class SendOtpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function testConstructorAssignsParameters()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);

        $this->assertEquals($email, $job->email);
        $this->assertEquals($otp, $job->otp);
    }

    public function testHandleMethodSendsMail()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function testHandleMethodSendsMailToCorrectEmail()
    {
        $email = 'user@example.com';
        $otp = '654321';

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function testHandleMethodWithDifferentEmailFormats()
    {
        $emails = [
            'user@example.com',
            'test.user@domain.co.uk',
            'admin+test@company.org',
            'user123@subdomain.example.net'
        ];

        foreach ($emails as $email) {
            $otp = '123456';
            $job = new SendOtp($email, $otp);
            $job->handle();

            Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
                return $mail->hasTo($email);
            });
        }
    }

    public function testHandleMethodWithDifferentOtpFormats()
    {
        $email = 'test@example.com';
        $otps = [
            '123456',
            '000000',
            '999999',
            '123456789',
            'ABC123',
            '123-456'
        ];

        foreach ($otps as $otp) {
            $job = new SendOtp($email, $otp);
            $job->handle();

            Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
                return $mail->hasTo($email);
            });
        }
    }

    public function testHandleMethodWithEmptyOtp()
    {
        $email = 'test@example.com';
        $otp = '';

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function testHandleMethodWithNullOtp()
    {
        $email = 'test@example.com';
        $otp = null;

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function testHandleMethodWithObjectOtp()
    {
        $email = 'test@example.com';
        $otp = (object) ['token' => '123456'];

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function testHandleMethodWithArrayOtp()
    {
        $email = 'test@example.com';
        $otp = ['token' => '123456'];

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function testJobCanBeSerialized()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);
        
        $serialized = serialize($job);
        $unserialized = unserialize($serialized);

        $this->assertEquals($email, $unserialized->email);
        $this->assertEquals($otp, $unserialized->otp);
    }

    public function testJobPropertiesArePublic()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);

        $this->assertTrue(property_exists($job, 'email'));
        $this->assertTrue(property_exists($job, 'otp'));
        
        // Test that properties are accessible
        $this->assertEquals($email, $job->email);
        $this->assertEquals($otp, $job->otp);
    }

    public function testHandleMethodCallsMailFacade()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);
        $job->handle();

        // Verify that mail was queued
        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function testHandleMethodWithSpecialCharactersInEmail()
    {
        $emails = [
            'test+tag@example.com',
            'user.name@domain.com',
            'user-name@sub.domain.com',
            'user_name@example.co.uk'
        ];

        foreach ($emails as $email) {
            $otp = '123456';
            $job = new SendOtp($email, $otp);
            $job->handle();

            Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
                return $mail->hasTo($email);
            });
        }
    }

    public function testHandleMethodMultipleTimes()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);
        
        // Call handle multiple times
        $job->handle();
        $job->handle();
        $job->handle();

        // Should queue 3 emails
        Mail::assertQueued(SendOtpMail::class, 3);
    }

    public function testJobWithWhitespaceInEmail()
    {
        $email = '  test@example.com  ';
        $otp = '123456';

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }
} 
