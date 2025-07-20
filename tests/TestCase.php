<?php

namespace Turahe\Otp\Test;

use Turahe\Otp\OtpServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            OtpServiceProvider::class,
        ];
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->initializeDirectory($this->getTempDirectory());

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => $this->getTempDirectory() . '/database.sqlite',
            'prefix'   => '',
        ]);
    }

    /**
     * @param Application $app
     */
    protected function setUpDatabase(Application $app)
    {
        file_put_contents($this->getTempDirectory() . '/database.sqlite', null);

        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('other_field')->nullable();
            $table->string('url')->nullable();
        });

        // Create otp_tokens table for testing
        Schema::create('otp_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('identity'); // email or number phone
            $table->string('token');
            $table->timestamp('expired');
            $table->timestamps();
        });
    }

    protected function initializeDirectory(string $directory)
    {
        if (File::isDirectory($directory)) {
            File::deleteDirectory($directory);
        }
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    protected function getTempDirectory(): string
    {
        return __DIR__ . '/temp';
    }
}
