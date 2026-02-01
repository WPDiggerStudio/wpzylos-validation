<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * Confirmed rule.
 *
 * Validates that a field has a matching confirmation field.
 * For example, 'password' must match 'password_confirmation'.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class ConfirmedRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        $confirmationField = $field . '_confirmation';
        return isset($data[$confirmationField]) && $value === $data[$confirmationField];
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute confirmation does not match.';
    }
}
