<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation;

/**
 * Rule interface.
 *
 * @package WPZylos\Framework\Validation
 */
interface RuleInterface
{
    /**
     * Validate a value.
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @param array<string, mixed> $data All data being validated
     * @return bool True if valid
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool;

    /**
     * Get the error message.
     *
     * @return string Message with :attribute and :paramN placeholders
     */
    public function message(): string;
}
