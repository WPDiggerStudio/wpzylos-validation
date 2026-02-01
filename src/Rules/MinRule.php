<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * Min rule.
 *
 * Validates minimum length/value/count.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class MinRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        $min = (int) ($parameters[0] ?? 0);

        if (is_string($value)) {
            return mb_strlen($value) >= $min;
        }

        if (is_array($value)) {
            return count($value) >= $min;
        }

        if (is_numeric($value)) {
            return $value >= $min;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field must be at least :param0.';
    }
}
