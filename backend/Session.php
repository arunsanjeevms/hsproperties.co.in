<?php
/**
 * HS Properties — Session Manager.
 *
 * Handles session lifecycle, CSRF token generation and validation,
 * flash messages, and multi-step form data persistence.
 */

declare(strict_types=1);

class Session
{
    /**
     * Start the session with secure settings.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isSecure,
            'httponly'  => true,
            'samesite'  => 'Lax',
        ]);

        session_name('hs_session');
        session_start();

        // Regenerate session ID periodically to prevent fixation
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) {
            if (!headers_sent()) {
                session_regenerate_id(true);
            }
            $_SESSION['_created'] = time();
        }
    }

    /**
     * Get a session value.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Set a session value.
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Check if a session key exists.
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session key.
     */
    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the session entirely.
     */
    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => $params['secure'],
                'httponly'  => $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]);
        }

        session_destroy();
    }

    // ── CSRF Token ───────────────────────────────────────────────────────

    /**
     * Generate and return a CSRF token.
     */
    public static function csrfToken(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * Render a hidden CSRF input field.
     */
    public static function csrfField(): string
    {
        return sprintf(
            '<input type="hidden" name="_csrf_token" value="%s">',
            htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Validate the submitted CSRF token.
     */
    public static function csrfValidate(?string $token = null): bool
    {
        $token = $token ?? ($_POST['_csrf_token'] ?? $_POST['_csrf'] ?? '');
        $valid = isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);

        // Regenerate token after validation
        unset($_SESSION['_csrf_token']);

        return $valid;
    }

    /**
     * Alias for csrfValidate.
     */
    public static function validateCsrf(?string $token = null): bool
    {
        return self::csrfValidate($token);
    }

    // ── Flash Messages ───────────────────────────────────────────────────

    /**
     * Set or get a flash message.
     * If $value is null, retrieves and clears the flash value.
     */
    public static function flash(string $key, mixed $value = null): mixed
    {
        if ($value === null) {
            return self::getFlash($key);
        }
        $_SESSION['_flash'][$key] = $value;
        return null;
    }

    /**
     * Alias for forget.
     */
    public static function remove(string $key): void
    {
        self::forget($key);
    }

    /**
     * Get and clear a flash message.
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    /**
     * Check if a flash message exists.
     */
    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    // ── Form Data Persistence ────────────────────────────────────────────

    /**
     * Store form data for a specific step in the multi-step wizard.
     */
    public static function setStepData(string $flow, int $step, array $data): void
    {
        $_SESSION['_intake'][$flow][$step] = $data;
    }

    /**
     * Get stored form data for a specific step.
     */
    public static function getStepData(string $flow, int $step): array
    {
        return $_SESSION['_intake'][$flow][$step] ?? [];
    }

    /**
     * Get all stored form data across all steps.
     */
    public static function getAllStepData(string $flow): array
    {
        return $_SESSION['_intake'][$flow] ?? [];
    }

    /**
     * Clear all intake data for a flow.
     */
    public static function clearIntake(string $flow): void
    {
        unset($_SESSION['_intake'][$flow]);
    }

    /**
     * Get the old input value for repopulating forms after validation errors.
     */
    public static function old(string $key, string $default = ''): string
    {
        return $_SESSION['_old_input'][$key] ?? $default;
    }

    /**
     * Store old input for repopulation.
     */
    public static function setOldInput(array $data): void
    {
        $_SESSION['_old_input'] = $data;
    }

    /**
     * Clear old input.
     */
    public static function clearOldInput(): void
    {
        unset($_SESSION['_old_input']);
    }
}
