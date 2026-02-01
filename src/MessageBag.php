<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation;

/**
 * Validation message bag.
 *
 * Collects validation errors per field.
 *
 * @package WPZylos\Framework\Validation
 */
class MessageBag
{
    /**
     * @var array<string, string[]> Error messages per field
     */
    private array $messages = [];

    /**
     * Add an error message.
     *
     * @param string $field Field name
     * @param string $message Error message
     * @return void
     */
    public function add(string $field, string $message): void
    {
        $this->messages[$field][] = $message;
    }

    /**
     * Check if there are any errors.
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->messages);
    }

    /**
     * Check if the field has errors.
     *
     * @param string $field Field name
     * @return bool
     */
    public function has(string $field): bool
    {
        return isset($this->messages[$field]) && !empty($this->messages[$field]);
    }

    /**
     * Get the first error for a field.
     *
     * @param string $field Field name
     * @return string|null
     */
    public function first(string $field): ?string
    {
        return $this->messages[$field][0] ?? null;
    }

    /**
     * Get all errors for a field.
     *
     * @param string $field Field name
     * @return string[]
     */
    public function get(string $field): array
    {
        return $this->messages[$field] ?? [];
    }

    /**
     * Get all error messages.
     *
     * @return array<string, string[]>
     */
    public function all(): array
    {
        return $this->messages;
    }

    /**
     * Get all messages as a flat array.
     *
     * @return string[]
     */
    public function flatten(): array
    {
        $flat = [];

        foreach ($this->messages as $fieldMessages) {
            foreach ($fieldMessages as $message) {
                $flat[] = $message;
            }
        }

        return $flat;
    }

    /**
     * Get count of all errors.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->flatten());
    }

    /**
     * Get a list of fields with errors.
     *
     * @return string[]
     */
    public function keys(): array
    {
        return array_keys($this->messages);
    }

    /**
     * Convert to array.
     *
     * @return array<string, string[]>
     */
    public function toArray(): array
    {
        return $this->messages;
    }
}
