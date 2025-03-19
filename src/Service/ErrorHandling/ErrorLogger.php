<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\ErrorHandling;

use Psr\Log\LoggerInterface;

class ErrorLogger
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logApiError(\Throwable $error, array $context = []): void
    {
        $this->logger->error('API Error: ' . $error->getMessage(), array_merge([
            'exception' => get_class($error),
            'code' => $error->getCode(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
        ], $context));
    }

    public function logConfigurationError(\Throwable $error): void
    {
        $this->logger->error('Configuration Error: ' . $error->getMessage(), [
            'exception' => get_class($error),
            'code' => $error->getCode(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
        ]);
    }

    public function logSystemError(\Throwable $error, array $context = []): void
    {
        $this->logger->critical('System Error: ' . $error->getMessage(), array_merge([
            'exception' => get_class($error),
            'code' => $error->getCode(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString()
        ], $context));
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }
}
