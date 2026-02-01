<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * URL rule.
 *
 * Validates that a field contains a valid URL.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class UrlRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field must be a valid URL.';
    }
}
