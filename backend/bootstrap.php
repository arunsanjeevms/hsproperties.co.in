<?php
/**
 * HS Properties — Application Bootstrap.
 *
 * Loads environment variables, establishes the database connection, starts
 * sessions, and sets up CSRF protection. Every page and API endpoint includes
 * this file to get a ready-to-use application context.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/FileUpload.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/models/Property.php';
require_once __DIR__ . '/models/SellerRequest.php';
require_once __DIR__ . '/models/BuyerRequest.php';
require_once __DIR__ . '/models/Document.php';
require_once __DIR__ . '/models/User.php';

// ── Load .env ────────────────────────────────────────────────────────────────
$envFile = APP_ROOT . '/.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key]    = $value;
                $_SERVER[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

/**
 * Read an environment variable with an optional default.
 */
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

// ── Database (Optional with Web3Forms) ───────────────────────────────────────
$db = null;
try {
    $db = Database::getInstance([
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', '3306'),
        'name' => env('DB_NAME', 'hsproperties'),
        'user' => env('DB_USER', 'root'),
        'pass' => env('DB_PASS', ''),
    ]);
} catch (\Throwable $e) {
    // Database is optional; Web3Forms handles submissions directly
}

// ── Session ──────────────────────────────────────────────────────────────────
Session::start();

// ── Error Handling ───────────────────────────────────────────────────────────
if (env('APP_DEBUG', 'false') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ── Timezone ─────────────────────────────────────────────────────────────────
date_default_timezone_set('Asia/Kolkata');
