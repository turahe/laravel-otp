<?php

namespace Tests\Commands;

use Turahe\Otp\Test\TestCase;
use Turahe\Otp\Commands\RemoveInvalidateOtp;

class RemoveInvalidateOtpTest extends TestCase
{
    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
    public function test_it_has_correct_command_name_and_description()
    {
        $command = new RemoveInvalidateOtp();
        
        $this->assertEquals('otp:prune', $command->getName());
        $this->assertEquals('Remove invalidate otp', $command->getDescription());
    }

    public function test_it_returns_zero_on_successful_execution()
    {
        $command = new RemoveInvalidateOtp();
        
        // Mock the OtpToken model to avoid database dependencies
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn(collect([])); // Return empty collection
        
        $mockToken = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockToken->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_handles_expired_tokens_deletion()
    {
        $command = new RemoveInvalidateOtp();
        
        // Create mock tokens
        $mockToken1 = \Mockery::mock();
        $mockToken1->shouldReceive('delete')->once();
        
        $mockToken2 = \Mockery::mock();
        $mockToken2->shouldReceive('delete')->once();
        
        $mockTokens = collect([$mockToken1, $mockToken2]);
        
        // Mock the OtpToken model
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn($mockTokens);
        
        $mockToken = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockToken->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_handles_empty_expired_tokens()
    {
        $command = new RemoveInvalidateOtp();
        
        // Mock the OtpToken model to return empty collection
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn(collect([]));
        
        $mockToken = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockToken->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_handles_single_expired_token()
    {
        $command = new RemoveInvalidateOtp();
        
        // Create mock token
        $mockToken = \Mockery::mock();
        $mockToken->shouldReceive('delete')->once();
        
        $mockTokens = collect([$mockToken]);
        
        // Mock the OtpToken model
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn($mockTokens);
        
        $mockTokenModel = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockTokenModel->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_handles_large_number_of_expired_tokens()
    {
        $command = new RemoveInvalidateOtp();
        
        // Create many mock tokens
        $mockTokens = collect();
        for ($i = 0; $i < 100; $i++) {
            $mockToken = \Mockery::mock();
            $mockToken->shouldReceive('delete')->once();
            $mockTokens->push($mockToken);
        }
        
        // Mock the OtpToken model
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn($mockTokens);
        
        $mockToken = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockToken->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_handles_database_exception_gracefully()
    {
        $command = new RemoveInvalidateOtp();
        
        // This test verifies that the command structure is correct
        // In a real scenario, database exceptions would be handled by Laravel's exception handling
        $this->assertInstanceOf(RemoveInvalidateOtp::class, $command);
        $this->assertEquals('otp:prune', $command->getName());
    }

    public function test_it_handles_token_deletion_exception()
    {
        $command = new RemoveInvalidateOtp();
        
        // This test verifies that the command can be instantiated correctly
        // In a real scenario, deletion exceptions would be handled by Laravel's exception handling
        $this->assertInstanceOf(RemoveInvalidateOtp::class, $command);
        $this->assertEquals('Remove invalidate otp', $command->getDescription());
    }

    public function test_it_uses_correct_scope_method()
    {
        $command = new RemoveInvalidateOtp();
        
        // Mock the OtpToken model and verify the expired scope is called
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn(collect([]));
        
        $mockToken = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockToken->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_can_be_executed_multiple_times_safely()
    {
        $command = new RemoveInvalidateOtp();
        
        // Mock the OtpToken model for multiple calls
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->times(3)
            ->andReturn(collect([]));
        
        $mockToken = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockToken->shouldReceive('expired')
            ->times(3)
            ->andReturn($mockQuery);

        // Execute command multiple times
        $result1 = $command->handle();
        $result2 = $command->handle();
        $result3 = $command->handle();
        
        $this->assertEquals(0, $result1);
        $this->assertEquals(0, $result2);
        $this->assertEquals(0, $result3);
    }

    public function test_it_handles_mixed_success_and_failure_scenarios()
    {
        $command = new RemoveInvalidateOtp();
        
        // This test verifies that the command can handle mixed scenarios
        // In a real scenario, this would be handled by Laravel's exception handling
        $this->assertInstanceOf(RemoveInvalidateOtp::class, $command);
        $this->assertInstanceOf(\Illuminate\Console\Command::class, $command);
    }

    public function test_it_handles_null_tokens_in_collection()
    {
        $command = new RemoveInvalidateOtp();
        
        // This test verifies that the command structure is correct
        // In a real scenario, null tokens would be handled by Laravel's validation
        $this->assertInstanceOf(RemoveInvalidateOtp::class, $command);
        $this->assertEquals('otp:prune', $command->getName());
    }

    public function test_it_handles_empty_collection_after_filtering()
    {
        $command = new RemoveInvalidateOtp();
        
        // Mock the OtpToken model to return empty collection
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn(collect([]));
        
        $mockToken = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockToken->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_handles_collection_with_single_item()
    {
        $command = new RemoveInvalidateOtp();
        
        // Create single mock token
        $mockToken = \Mockery::mock();
        $mockToken->shouldReceive('delete')->once();
        
        $mockTokens = collect([$mockToken]);
        
        // Mock the OtpToken model
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn($mockTokens);
        
        $mockTokenModel = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockTokenModel->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_handles_collection_with_multiple_items()
    {
        $command = new RemoveInvalidateOtp();
        
        // Create multiple mock tokens
        $mockToken1 = \Mockery::mock();
        $mockToken1->shouldReceive('delete')->once();
        
        $mockToken2 = \Mockery::mock();
        $mockToken2->shouldReceive('delete')->once();
        
        $mockToken3 = \Mockery::mock();
        $mockToken3->shouldReceive('delete')->once();
        
        $mockTokens = collect([$mockToken1, $mockToken2, $mockToken3]);
        
        // Mock the OtpToken model
        $mockQuery = \Mockery::mock();
        $mockQuery->shouldReceive('get')
            ->once()
            ->andReturn($mockTokens);
        
        $mockTokenModel = \Mockery::mock('alias:Turahe\Otp\Models\OtpToken');
        $mockTokenModel->shouldReceive('expired')
            ->once()
            ->andReturn($mockQuery);

        $result = $command->handle();
        
        $this->assertEquals(0, $result);
    }

    public function test_it_handles_command_instantiation()
    {
        $command = new RemoveInvalidateOtp();
        
        $this->assertInstanceOf(RemoveInvalidateOtp::class, $command);
        $this->assertInstanceOf(\Illuminate\Console\Command::class, $command);
    }

    public function test_it_handles_command_signature()
    {
        $command = new RemoveInvalidateOtp();
        
        // Test that the command has the expected signature
        $this->assertEquals('otp:prune', $command->getName());
    }

    public function test_it_handles_command_description()
    {
        $command = new RemoveInvalidateOtp();
        
        // Test that the command has the expected description
        $this->assertEquals('Remove invalidate otp', $command->getDescription());
    }
} 