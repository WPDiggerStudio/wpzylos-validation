<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation;

use Exception;
use Throwable;

/**
 * Validation exception.
 *
 * Thrown when validation fails on a validated () call.
 *
 * @package WPZylos\Framework\Validation
 */
class ValidationException extends Exception
{
    /**
     * @var MessageBag Validation errors
     */
    private MessageBag $errors;

    /**
     * Create exception.
     *
     * @param MessageBag $errors Error bag
     * @param string $message Exception message
     * @param int $code Error code
     * @param Throwable|null $previous Previous exception
     */
    public function __construct(
        MessageBag $errors,
        string $message = 'The given data was invalid.',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get validation errors.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        return $this->errors;
    }
}
