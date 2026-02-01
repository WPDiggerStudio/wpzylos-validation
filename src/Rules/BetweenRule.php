<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation\Rules;

use WPZylos\Framework\Validation\RuleInterface;

/**
 * Between rule.
 *
 * Validates that a field value is between a minimum and maximum.
 *
 * @package WPZylos\Framework\Validation\Rules
 */
class BetweenRule implements RuleInterface
{
    /**
     * {@inheritDoc}
     */
    public function passes(string $field, mixed $value, array $parameters, array $data): bool
    {
        $min = (int) ($parameters[0] ?? 0);
        $max = (int) ($parameters[1] ?? 0);

        if (is_string($value)) {
            $length = mb_strlen($value);
            return $length >= $min && $length <= $max;
        }

        if (is_array($value)) {
            $count = count($value);
            return $count >= $min && $count <= $max;
        }

        if (is_numeric($value)) {
            return $value >= $min && $value <= $max;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function message(): string
    {
        return 'The :attribute field must be between :param0 and :param1.';
    }
}
