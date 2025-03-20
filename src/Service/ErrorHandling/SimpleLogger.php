<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\ErrorHandling;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class SimpleLogger implements LoggerInterface
{
    private string $channel;

    public function __construct(string $channel = 'bow_preishoheit')
    {
        $this->channel = $channel;
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        // Simple implementation that writes to the error log
        // This avoids using monolog directly while still providing logging
        $logMessage = sprintf('[%s] %s: %s', $this->channel, strtoupper($level), $message);
        
        // Use error_log as a fallback mechanism
        error_log($logMessage);
        
        // Additional context can be logged if needed
        if (!empty($context)) {
            error_log('Context: ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
    }
}
