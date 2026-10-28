<?php
/**
 * HS Properties — Backend Helpers.
 *
 * Utility functions used across controllers, models, and views.
 */

declare(strict_types=1);

/**
 * Generate a unique reference number for requests.
 *
 * Format: HS-YYYY-CCC-NNNN
 *   - YYYY = year
 *   - CCC  = city code (first 3 letters)
 *   - NNNN = random 4-digit number
 */
function generate_reference(string $city = ''): string
{
    $cityCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $city) ?: 'GEN', 0, 3));
    $number   = random_int(1000, 9999);
    return sprintf('HS-%s-%s-%d', date('Y'), $cityCode, $number);
}

/**
 * Redirect to a URL and exit.
 */
function redirect(string $url, int $statusCode = 302): never
{
    header("Location: $url", true, $statusCode);
    exit;
}

/**
 * Return a JSON response and exit.
 */
function json_response(array $data, int $statusCode = 200): never
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Check if the current request is POST.
 */
function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if the request is an AJAX/API request.
 */
function is_ajax(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get the client's IP address.
 */
function client_ip(): string
{
    return $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_X_REAL_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '0.0.0.0';
}

/**
 * Log an admin activity.
 */
function log_activity(string $action, string $entityType, ?int $entityId = null, mixed $details = null): void
{
    try {
        $detailsValue = null;
        if (is_array($details)) {
            $detailsValue = json_encode($details, JSON_UNESCAPED_UNICODE);
        } elseif (is_string($details)) {
            $detailsValue = $details;
        }

        Database::getInstance()->insert('activity_log', [
            'user_id'     => Auth::id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'details'     => $detailsValue,
            'ip_address'  => client_ip(),
        ]);
    } catch (Throwable) {
        // Silently fail — activity logging should never break the app
    }
}

/**
 * Format a price for display (Indian Rupee format).
 */
function format_price(string $amount): string
{
    $number = (float) str_replace([',', ' '], '', $amount);
    if ($number >= 10000000) {
        return '₹ ' . number_format($number / 10000000, 2) . ' Cr';
    } elseif ($number >= 100000) {
        return '₹ ' . number_format($number / 100000, 2) . ' L';
    }
    return '₹ ' . number_format($number, 0, '.', ',');
}

/**
 * Truncate text to a given length.
 */
function truncate(string $text, int $length = 100, string $suffix = '…'): string
{
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * Get the time elapsed since a given date.
 */
function time_ago(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = (int) floor($diff / 60);
        return "$mins minute" . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = (int) floor($diff / 3600);
        return "$hours hour" . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = (int) floor($diff / 86400);
        return "$days day" . ($days > 1 ? 's' : '') . ' ago';
    }

    return date('M j, Y', $time);
}

/**
 * Check CSRF token and abort with 403 on failure.
 */
function csrf_check(): void
{
    if (!Session::csrfValidate()) {
        if (is_ajax()) {
            json_response(['error' => 'Invalid security token. Please refresh the page.'], 403);
        }
        http_response_code(403);
        die('Security token validation failed. Please go back and try again.');
    }
}
