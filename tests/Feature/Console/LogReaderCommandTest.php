<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Tests\TestCase;
use Symfony\Component\Console\Exception\InvalidOptionException;

class LogReaderCommandTest extends TestCase
{
    public function testNoPathOptionException(): void
    {
        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('-p or --path should be provided [-h for more info]');

        $this->artisan('log:reader')->assertExitCode(0);
    }

    public function testInvalidModeException(): void
    {
        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage('Supported modes: "views" and "unique"');

        $this->artisan('log:reader', [
            '-p' => __DIR__ . '/Samples/views.log',
            '--mode' => 'phpUnitIncorrect'
        ]);
    }

    public function testRunCommandUniqueMode(): void
    {
        $this->artisan('log:reader', [
            '-p' => __DIR__ . '/Samples/views.log',
            '-m' => 'unique'
        ])
            ->expectsOutput('| Path     | Unique Views |')
            ->expectsOutput('| /path    | 2            |')
            ->expectsOutput('| /path2   | 2            |')
            ->expectsOutput('| anything | 1            |')
            ->expectsOutput('| a        | 1            |')
            ->assertExitCode(0);
    }

    public function testRunCommand(): void
    {
        $this->artisan('log:reader', [
            '-p' => __DIR__ . '/Samples/views.log',
            '-m' => 'views'
        ])
            ->expectsOutput('| Path     | Views |')
            ->expectsOutput('| /path    | 6     |')
            ->expectsOutput('| /path2   | 3     |')
            ->expectsOutput('| anything | 2     |')
            ->expectsOutput('| a        | 1     |')
            ->assertExitCode(0);
    }
}
