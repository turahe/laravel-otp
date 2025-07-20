<?php

namespace Turahe\Otp\Test\Jobs;

use Illuminate\Support\Facades\Mail;
use Turahe\Otp\Jobs\SendOtp;
use Turahe\Otp\Mail\SendOtp as SendOtpMail;
use Turahe\Otp\Test\TestCase;

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

    public function test_constructor_assigns_parameters()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);

        $this->assertEquals($email, $job->email);
        $this->assertEquals($otp, $job->otp);
    }

    public function test_handle_method_sends_mail()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function test_handle_method_sends_mail_to_correct_email()
    {
        $email = 'user@example.com';
        $otp = '654321';

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function test_handle_method_with_different_email_formats()
    {
        $emails = [
            'user@example.com',
            'test.user@domain.co.uk',
            'admin+test@company.org',
            'user123@subdomain.example.net',
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

    public function test_handle_method_with_different_otp_formats()
    {
        $email = 'test@example.com';
        $otps = [
            '123456',
            '000000',
            '999999',
            '123456789',
            'ABC123',
            '123-456',
        ];

        foreach ($otps as $otp) {
            $job = new SendOtp($email, $otp);
            $job->handle();

            Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
                return $mail->hasTo($email);
            });
        }
    }

    public function test_handle_method_with_empty_otp()
    {
        $email = 'test@example.com';
        $otp = '';

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function test_handle_method_with_null_otp()
    {
        $email = 'test@example.com';
        $otp = null;

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function test_handle_method_with_object_otp()
    {
        $email = 'test@example.com';
        $otp = (object) ['token' => '123456'];

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function test_handle_method_with_array_otp()
    {
        $email = 'test@example.com';
        $otp = ['token' => '123456'];

        $job = new SendOtp($email, $otp);
        $job->handle();

        Mail::assertQueued(SendOtpMail::class, function ($mail) use ($email) {
            return $mail->hasTo($email);
        });
    }

    public function test_job_can_be_serialized()
    {
        $email = 'test@example.com';
        $otp = '123456';

        $job = new SendOtp($email, $otp);

        $serialized = serialize($job);
        $unserialized = unserialize($serialized);

        $this->assertEquals($email, $unserialized->email);
        $this->assertEquals($otp, $unserialized->otp);
    }

    public function test_job_properties_are_public()
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

    public function test_handle_method_calls_mail_facade()
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

    public function test_handle_method_with_special_characters_in_email()
    {
        $emails = [
            'test+tag@example.com',
            'user.name@domain.com',
            'user-name@sub.domain.com',
            'user_name@example.co.uk',
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

    public function test_handle_method_multiple_times()
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

    public function test_job_with_whitespace_in_email()
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
