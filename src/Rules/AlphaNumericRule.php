<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * AlphaNumeric rule.
 *
 * Validates that a field contains only alphanumeric characters.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class AlphaNumericRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        return is_string($value) && preg_match('/^[\pL\pM\pN]+$/u', $value);
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field must only contain letters and numbers.';
    }
}
