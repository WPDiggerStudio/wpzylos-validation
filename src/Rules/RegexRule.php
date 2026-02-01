<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * Regex rule.
 *
 * Validates that a field matches a regular expression.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class RegexRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        $pattern = $parameters[0] ?? '';
        return preg_match($pattern, (string) $value) === 1;
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field format is invalid.';
    }
}
