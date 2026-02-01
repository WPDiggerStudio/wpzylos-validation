<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * Numeric rule.
 *
 * Validates that a field contains a numeric value.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class NumericRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        return is_numeric($value);
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field must be a number.';
    }
}
