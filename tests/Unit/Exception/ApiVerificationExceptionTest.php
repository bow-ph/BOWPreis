<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Exception;

use BOW\Preishoheit\Exception\ApiVerificationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiVerificationExceptionTest extends TestCase
{
    public function testConstructor(): void
    {
        $message = 'Test error message';
        $exception = new ApiVerificationException($message);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $exception->getCode());
    }

    public function testConstructorWithCustomCode(): void
    {
        $message = 'Test error message';
        $code = Response::HTTP_UNAUTHORIZED;
        $exception = new ApiVerificationException($message, $code);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }
}
