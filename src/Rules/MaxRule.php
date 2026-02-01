<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * Max rule.
 *
 * Validates maximum length/value/count.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class MaxRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        $max = (int) ($parameters[0] ?? 0);

        if (is_string($value)) {
            return mb_strlen($value) <= $max;
        }

        if (is_array($value)) {
            return count($value) <= $max;
        }

        if (is_numeric($value)) {
            return $value <= $max;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field must be at most :param0.';
    }
}
