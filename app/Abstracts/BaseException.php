<?php

namespace App\Abstracts;

use Exception;

abstract class BaseException extends Exception
{
    protected string $errorCode;
    protected array $context;

    public function __construct(string $message, string $errorCode = '', array $context = [], int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errorCode = $errorCode;
        $this->context = $context;
    }

    /**
     * Get error code
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get context data
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Convert exception to array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'error_code' => $this->getErrorCode(),
            'context' => $this->getContext(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }

    /**
     * Create exception with context
     */
    public static function withContext(string $message, array $context = [], string $errorCode = ''): static
    {
        return new static($message, $errorCode, $context);
    }
}