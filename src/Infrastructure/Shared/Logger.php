<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class Logger implements LoggerInterface
{
    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {}

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(LogLevel::WARNING, $message);
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(LogLevel::EMERGENCY, $message);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(LogLevel::ALERT, $message);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(LogLevel::CRITICAL, $message);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(LogLevel::ERROR, $message);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(LogLevel::NOTICE, $message);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(LogLevel::INFO, $message);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog(LogLevel::DEBUG, $message);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->writeLog("log", $message);
    }

    private function writeLog(string $level, string|\Stringable $message): void
    {
        $logsLocation = $this->parameterBag->get("app.logs.location");
        if (empty($logsLocation)) {
            return;
        }

        $formatted = sprintf("[%s] %s: %s\n", strtoupper($level), date('Y-m-d H:i:s'), $message);
        file_put_contents($logsLocation, $formatted, FILE_APPEND);
    }
}
