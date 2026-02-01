<?php

declare(strict_types=1);

namespace WPZylos\Framework\Validation;

use WPZylos\Framework\Http\Request;
use WPZylos\Framework\I18n\Translator;

/**
 * Form requests base class.
 *
 * Combines authorization, sanitization, and validation.
 *
 * @package WPZylos\Framework\Validation
 */
abstract class FormRequest
{
    /**
     * @var Request HTTP request
     */
    protected Request $request;

    /**
     * @var Translator|null Translator for messages
     */
    protected ?Translator $translator;

    /**
     * @var Validator|null Validator instance
     */
    private ?Validator $validator = null;

    /**
     * @var array<string, mixed>|null Sanitized data
     */
    private ?array $sanitized = null;

    /**
     * Create a form request.
     *
     * @param Request $request HTTP request
     * @param Translator|null $translator Translator
     */
    public function __construct(Request $request, ?Translator $translator = null)
    {
        $this->request = $request;
        $this->translator = $translator;
    }

    /**
     * Check if the user is authorized for this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get validation rules.
     *
     * @return array<string, string|string[]>
     */
    abstract public function rules(): array;

    /**
     * Get field sanitization mapping.
     *
     * Return array of a field => sanitizer type.
     * Types: text, textarea, HTML, email, url, int, absint, float, bool, slug, key
     *
     * @return array<string, string>
     */
    public function sanitize(): array
    {
        return [];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Get custom attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [];
    }

    /**
     * Validate the request.
     *
     * @return bool True if valid
     */
    public function validate(): bool
    {
        return $this->getValidator()->passes();
    }

    /**
     * Check if validation fails.
     *
     * @return bool
     */
    public function fails(): bool
    {
        return $this->getValidator()->fails();
    }

    /**
     * Get validation errors.
     *
     * @return MessageBag
     */
    public function errors(): MessageBag
    {
        return $this->getValidator()->errors();
    }

    /**
     * Get validated and sanitized data.
     *
     * @return array<string, mixed>
     * @throws ValidationException If validation fails
     */
    public function validated(): array
    {
        return $this->getValidator()->validated();
    }

    /**
     * Get sanitized input data.
     *
     * @return array<string, mixed>
     */
    public function data(): array
    {
        if ($this->sanitized !== null) {
            return $this->sanitized;
        }

        $input = $this->request->all();
        $sanitizeMap = $this->sanitize();

        if (empty($sanitizeMap)) {
            $this->sanitized = $input;
            return $this->sanitized;
        }

        $this->sanitized = [];
        foreach ($input as $key => $value) {
            if (isset($sanitizeMap[$key])) {
                $this->sanitized[$key] = $this->applySanitizer($value, $sanitizeMap[$key]);
            } else {
                $this->sanitized[$key] = $value;
            }
        }

        return $this->sanitized;
    }

    /**
     * Apply a sanitizer to a value.
     *
     * @param mixed $value Value to sanitize
     * @param string $type Sanitizer type
     * @return mixed
     */
    private function applySanitizer(mixed $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'textarea' => sanitize_textarea_field((string) $value),
            'html' => wp_kses_post((string) $value),
            'email' => sanitize_email((string) $value),
            'url' => esc_url_raw((string) $value),
            'int', 'integer' => (int) $value,
            'absint' => absint($value),
            'float' => (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'bool', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'slug' => sanitize_title((string) $value),
            'key' => sanitize_key((string) $value),
            default => sanitize_text_field((string) $value),
        };
    }

    /**
     * Get or create validator.
     *
     * @return Validator
     */
    private function getValidator(): Validator
    {
        if ($this->validator !== null) {
            return $this->validator;
        }

        $this->validator = new Validator(
            $this->data(),
            $this->rules(),
            $this->messages(),
            $this->attributes(),
            $this->translator
        );

        return $this->validator;
    }
}
