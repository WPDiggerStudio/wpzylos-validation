<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * Required rule.
 *
 * Validates that a field is present and not empty.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class RequiredRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_array($value) && empty($value)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field is required.';
    }
}
