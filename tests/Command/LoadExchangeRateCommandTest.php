<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\LoadExchangeRateCommand;
use App\Service\ExchangeRateService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(LoadExchangeRateCommand::class)]
class LoadExchangeRateCommandTest extends TestCase
{
    public function testExecuteCallsServiceAndOutputsMessages(): void
    {
        $exchangeRateService = $this->createMock(ExchangeRateService::class);
        $exchangeRateService->expects($this->once())
            ->method('loadAndSaveRates');

        $command = new LoadExchangeRateCommand($exchangeRateService);

        $application = new Application();
        $application->add($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Loading currency rates...', $output);
        $this->assertStringContainsString('Currency rates loaded successfully.', $output);
    }
}
