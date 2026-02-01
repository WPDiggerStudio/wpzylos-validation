<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * In rule.
 *
 * Validates that a field value is in a list of allowed values.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class InRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        return in_array($value, $parameters, true);
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field must be one of: :param0.';
    }
}
