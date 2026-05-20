<?php

namespace Tests\Unit\Console;

use App\Console\Commands\NotifyLowDataUsageCommand;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class NotifyLowDataUsageCommandTest extends TestCase
{
    public function test_command_uses_expected_signature_and_alias(): void
    {
        $command = new NotifyLowDataUsageCommand();
        $reflection = new ReflectionClass($command);

        $signature = $reflection->getProperty('signature');
        $signature->setAccessible(true);

        $aliases = $reflection->getProperty('aliases');
        $aliases->setAccessible(true);

        $this->assertSame('notificar:consumo-esim', $signature->getValue($command));
        $this->assertContains('esim:notify-low-data', $aliases->getValue($command));
    }
}
