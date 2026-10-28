<?php
/**
 * HS Properties — Input Validator.
 *
 * Fluent validation for form inputs with error message collection.
 * Usage:
 *   $v = new Validator($_POST);
 *   $v->required('city', 'City is required');
 *   $v->email('contact_email', 'Please enter a valid email');
 *   if ($v->fails()) { $errors = $v->errors(); }
 */

declare(strict_types=1);

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Check if validation failed.
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Check if validation passed.
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get all validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get the first error for a field, or null.
     */
    public function error(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Get a validated/sanitised value.
     */
    public function value(string $field, string $default = ''): string
    {
        $val = $this->data[$field] ?? $default;
        return is_string($val) ? trim($val) : $default;
    }

    /**
     * Get an array value (e.g. checkboxes).
     */
    public function arrayValue(string $field): array
    {
        $val = $this->data[$field] ?? [];
        return is_array($val) ? $val : [];
    }

    /**
     * Get all sanitised data.
     */
    public function validated(): array
    {
        $clean = [];
        foreach ($this->data as $k => $v) {
            if ($k[0] === '_') {
                continue; // skip _csrf_token, _old_input, etc.
            }
            $clean[$k] = is_string($v) ? trim($v) : $v;
        }
        return $clean;
    }

    // ── Validation Rules ─────────────────────────────────────────────────

    /**
     * Field must be present and non-empty.
     */
    public function required(string $field, ?string $message = null): self
    {
        $val = $this->value($field);
        if ($val === '') {
            $this->errors[$field] = $message ?? "$field is required.";
        }
        return $this;
    }

    /**
     * Field must be a valid email.
     */
    public function email(string $field, ?string $message = null): self
    {
        $val = $this->value($field);
        if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message ?? 'Please enter a valid email address.';
        }
        return $this;
    }

    /**
     * Field must match a regex pattern.
     */
    public function pattern(string $field, string $regex, ?string $message = null): self
    {
        $val = $this->value($field);
        if ($val !== '' && !preg_match($regex, $val)) {
            $this->errors[$field] = $message ?? "$field format is invalid.";
        }
        return $this;
    }

    /**
     * Field must be at most $max characters.
     */
    public function maxLength(string $field, int $max, ?string $message = null): self
    {
        $val = $this->value($field);
        if ($val !== '' && mb_strlen($val) > $max) {
            $this->errors[$field] = $message ?? "$field must be at most $max characters.";
        }
        return $this;
    }

    /**
     * Field must be at least $min characters.
     */
    public function minLength(string $field, int $min, ?string $message = null): self
    {
        $val = $this->value($field);
        if ($val !== '' && mb_strlen($val) < $min) {
            $this->errors[$field] = $message ?? "$field must be at least $min characters.";
        }
        return $this;
    }

    /**
     * Field must be one of the allowed values.
     */
    public function in(string $field, array $allowed, ?string $message = null): self
    {
        $val = $this->value($field);
        if ($val !== '' && !in_array($val, $allowed, true)) {
            $this->errors[$field] = $message ?? "Invalid selection for $field.";
        }
        return $this;
    }

    /**
     * Field must be numeric.
     */
    public function numeric(string $field, ?string $message = null): self
    {
        $val = $this->value($field);
        // Allow Indian number formats (commas)
        $cleaned = str_replace([',', ' '], '', $val);
        if ($val !== '' && !is_numeric($cleaned)) {
            $this->errors[$field] = $message ?? "$field must be a number.";
        }
        return $this;
    }

    /**
     * Validate an Indian PIN code.
     */
    public function pincode(string $field, ?string $message = null): self
    {
        return $this->pattern($field, '/^[0-9]{6}$/', $message ?? 'Please enter a valid 6-digit PIN code.');
    }

    /**
     * Validate a phone number.
     */
    public function phone(string $field, ?string $message = null): self
    {
        return $this->pattern(
            $field,
            '/^[\+]?[0-9\s\-\(\)]{7,20}$/',
            $message ?? 'Please enter a valid phone number.'
        );
    }

    /**
     * Validate a year (4 digits).
     */
    public function year(string $field, ?string $message = null): self
    {
        return $this->pattern($field, '/^[0-9]{4}$/', $message ?? 'Please enter a valid year.');
    }

    /**
     * Custom validation rule with a callback.
     */
    public function custom(string $field, callable $callback, ?string $message = null): self
    {
        $val = $this->value($field);
        if ($val !== '' && !$callback($val, $this->data)) {
            $this->errors[$field] = $message ?? "$field is invalid.";
        }
        return $this;
    }

    /**
     * Add a manual error.
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }
}
