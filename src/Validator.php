<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation;

use WPZylos\Framework\I18n\Translator;

/**
 * Validator.
 *
 * Validates data against rules with localized error messages.
 *
 * @package WPZylos\Framework\Validation
 */
class Validator
{
    /**
     * @var array<string, mixed> Data to validate
     */
    private array $data;

    /**
     * @var array<string, string|string[]> Validation rules
     */
    private array $rules;

    /**
     * @var array<string, string> Custom messages
     */
    private array $customMessages;

    /**
     * @var array<string, string> Custom attribute names
     */
    private array $customAttributes;

    /**
     * @var Translator|null Translator for messages
     */
    private ?Translator $translator;

    /**
     * @var MessageBag Error bag
     */
    private MessageBag $errors;

    /**
     * @var bool Whether validation has run
     */
    private bool $validated = false;

    /**
     * @var array<string, RuleInterface> Extended rules
     */
    private static array $extensions = [];

    /**
     * @var array<string, string> Default messages
     */
    private static array $defaultMessages = [
        'required' => 'The :attribute field is required.',
        'string' => 'The :attribute field must be a string.',
        'integer' => 'The :attribute field must be an integer.',
        'boolean' => 'The :attribute field must be true or false.',
        'array' => 'The :attribute field must be an array.',
        'email' => 'The :attribute field must be a valid email address.',
        'url' => 'The :attribute field must be a valid URL.',
        'min' => 'The :attribute field must be at least :param0.',
        'max' => 'The :attribute field must be at most :param0.',
        'in' => 'The :attribute field must be one of: :param0.',
        'regex' => 'The :attribute field format is invalid.',
        'numeric' => 'The :attribute field must be a number.',
        'nullable' => '', // No message always passes
    ];

    /**
     * Create validator.
     *
     * @param array<string, mixed> $data Data to validate
     * @param array<string, string|string[]> $rules Validation rules
     * @param array<string, string> $messages Custom messages
     * @param array<string, string> $attributes Custom attribute names
     * @param Translator|null $translator Translator for messages
     */
    public function __construct(
        array $data,
        array $rules,
        array $messages = [],
        array $attributes = [],
        ?Translator $translator = null
    ) {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $messages;
        $this->customAttributes = $attributes;
        $this->translator = $translator;
        $this->errors = new MessageBag();
    }

    /**
     * Run validation.
     *
     * @return bool True if valid
     */
    public function validate(): bool
    {
        $this->errors = new MessageBag();

        foreach ($this->rules as $field => $rules) {
            $this->validateField($field, $rules);
        }

        $this->validated = true;
        return !$this->errors->hasErrors();
    }

    /**
     * Check if validation fails.
     *
     * @return bool
     */
    public function fails(): bool
    {
        if (!$this->validated) {
            $this->validate();
        }

        return $this->errors->hasErrors();
    }

    /**
     * Check if validation passes.
     *
     * @return bool
     */
    public function passes(): bool
    {
        return !$this->fails();
    }

    /**
     * Get validation errors.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        if (!$this->validated) {
            $this->validate();
        }

        return $this->errors;
    }

    /**
     * Get validated data (only fields with rules).
     *
     * @return array<string, mixed>
     * @throws ValidationException If validation fails
     */
    public function validated(): array
    {
        if ($this->fails()) {
            throw new ValidationException($this->errors);
        }

        return array_intersect_key($this->data, $this->rules);
    }

    /**
     * Validate a single field.
     *
     * @param string $field Field name
     * @param string|string[] $rules Rules to apply
     * @return void
     */
    private function validateField(string $field, string|array $rules): void
    {
        $rulesArray = is_string($rules) ? explode('|', $rules) : $rules;
        $value = $this->data[$field] ?? null;
        $isNullable = in_array('nullable', $rulesArray, true);

        // Skip validation if nullable and empty
        if ($isNullable && ($value === null || $value === '')) {
            return;
        }

        foreach ($rulesArray as $rule) {
            if ($rule === 'nullable') {
                continue;
            }

            $this->validateRule($field, $value, $rule);
        }
    }

    /**
     * Validate a single rule.
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $rule Rule definition
     * @return void
     */
    private function validateRule(string $field, mixed $value, string $rule): void
    {
        [$ruleName, $parameters] = $this->parseRule($rule);

        // Check extensions first
        if (isset(self::$extensions[$ruleName])) {
            $ruleInstance = self::$extensions[$ruleName];
            if (!$ruleInstance->passes($field, $value, $parameters, $this->data)) {
                $this->addError($field, $ruleName, $parameters, $ruleInstance->message());
            }
            return;
        }

        // Built-in rules
        $method = 'validate' . ucfirst($ruleName);
        if (method_exists($this, $method)) {
            if (!$this->$method($field, $value, $parameters)) {
                $this->addError($field, $ruleName, $parameters);
            }
            return;
        }

        throw new \InvalidArgumentException("Unknown validation rule: {$ruleName}");
    }

    /**
     * Parse rule string into name and parameters.
     *
     * @param string $rule Rule string (e.g., 'min:5')
     * @return array{0: string, 1: array<mixed>}
     */
    private function parseRule(string $rule): array
    {
        if (!str_contains($rule, ':')) {
            return [$rule, []];
        }

        [$name, $paramString] = explode(':', $rule, 2);
        $parameters = explode(',', $paramString);

        return [$name, $parameters];
    }

    /**
     * Add validation error.
     *
     * @param string $field Field name
     * @param string $rule Rule name
     * @param array<mixed> $parameters Rule parameters
     * @param string|null $customMessage Custom message
     * @return void
     */
    private function addError(
        string $field,
        string $rule,
        array $parameters,
        ?string $customMessage = null
    ): void {
        $message = $this->getMessage($field, $rule, $customMessage);
        $message = $this->replacePlaceholders($message, $field, $parameters);
        $this->errors->add($field, $message);
    }

    /**
     * Get an error message for a rule.
     *
     * @param string $field Field name
     * @param string $rule Rule name
     * @param string|null $customMessage Custom message override
     * @return string
     */
    private function getMessage(string $field, string $rule, ?string $customMessage = null): string
    {
        // Check field-specific custom message
        $key = "{$field}.{$rule}";
        if (isset($this->customMessages[$key])) {
            return $this->customMessages[$key];
        }

        // Check rule-level custom message
        if (isset($this->customMessages[$rule])) {
            return $this->customMessages[$rule];
        }

        // Custom message from extension
        if ($customMessage !== null) {
            return $customMessage;
        }

        // Default message (translate if translator available)
        $message = self::$defaultMessages[$rule] ?? 'The :attribute field is invalid.';

        if ($this->translator !== null) {
            return $this->translator->translate($message);
        }

        return $message;
    }

    /**
     * Replace placeholders in a message.
     *
     * @param string $message Message with placeholders
     * @param string $field Field name
     * @param array<mixed> $parameters Rule parameters
     * @return string
     */
    private function replacePlaceholders(string $message, string $field, array $parameters): string
    {
        $attribute = $this->customAttributes[$field] ?? str_replace('_', ' ', $field);
        $message = str_replace(':attribute', $attribute, $message);

        foreach ($parameters as $index => $param) {
            $message = str_replace(":param{$index}", (string) $param, $message);
        }

        return $message;
    }

    // =========================================================================
    // Built-in Validation Rules
    // =========================================================================

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateRequired(string $field, mixed $value, array $params): bool
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
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateString(string $field, mixed $value, array $params): bool
    {
        return is_string($value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateInteger(string $field, mixed $value, array $params): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateInt(string $field, mixed $value, array $params): bool
    {
        return $this->validateInteger($field, $value, $params);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateNumeric(string $field, mixed $value, array $params): bool
    {
        return is_numeric($value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateBoolean(string $field, mixed $value, array $params): bool
    {
        return in_array($value, [true, false, 0, 1, '0', '1', 'true', 'false'], true);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateArray(string $field, mixed $value, array $params): bool
    {
        return is_array($value);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateEmail(string $field, mixed $value, array $params): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateUrl(string $field, mixed $value, array $params): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateMin(string $field, mixed $value, array $params): bool
    {
        $min = (int) ($params[0] ?? 0);

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
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateMax(string $field, mixed $value, array $params): bool
    {
        $max = (int) ($params[0] ?? 0);

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
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateIn(string $field, mixed $value, array $params): bool
    {
        return in_array($value, $params, true);
    }

    /**
     * @param string $field
     * @param mixed $value
     * @param array<mixed> $params
     * @return bool
     */
    private function validateRegex(string $field, mixed $value, array $params): bool
    {
        $pattern = $params[0] ?? '';
        return preg_match($pattern, (string) $value) === 1;
    }

    /**
     * Extend validator with custom rule.
     *
     * @param string $name Rule name
     * @param RuleInterface $rule Rule implementation
     * @return void
     */
    public static function extend(string $name, RuleInterface $rule): void
    {
        self::$extensions[$name] = $rule;
    }
}
