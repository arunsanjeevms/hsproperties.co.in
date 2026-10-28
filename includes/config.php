<?php
/**
 * HS Properties — Application configuration & shared helpers.
 *
 * Frontend stage: no database connection is opened here yet. When the MySQL
 * layer is introduced, add the PDO bootstrap in this file so every page picks
 * it up without further changes.
 */

declare(strict_types=1);

/** Absolute filesystem root of the application. */
define('APP_ROOT', dirname(__DIR__));

/**
 * Base URL path the app is served from. Empty string when served from the
 * document root; set to e.g. '/hs-properties' when served from a subfolder.
 */
define('BASE_URL', '');

define('SITE_NAME', 'HS Properties');
define('SITE_TAGLINE', 'Private Advisory');

/**
 * Web3Forms Access Key helper.
 */
function web3forms_key(): string
{
    if (function_exists('env')) {
        $key = env('WEB3FORMS_ACCESS_KEY');
        if (!empty($key) && $key !== 'YOUR_WEB3FORMS_ACCESS_KEY') {
            return (string)$key;
        }
    }
    $envVal = $_ENV['WEB3FORMS_ACCESS_KEY'] ?? getenv('WEB3FORMS_ACCESS_KEY');
    if (!empty($envVal) && $envVal !== 'YOUR_WEB3FORMS_ACCESS_KEY') {
        return (string)$envVal;
    }
    return '00000000-0000-0000-0000-000000000000';
}

/**
 * Build a site-relative URL.
 */
function url(string $path = ''): string
{
    $clean = ltrim($path, '/');
    if ($clean === '' || $clean === 'index.php') {
        return 'index.html';
    }
    if (str_ends_with($clean, '.php')) {
        $clean = substr($clean, 0, -4) . '.html';
    }
    return $clean;
}

/**
 * Escape a value for safe output in HTML.
 *
 * Every dynamic value printed in a template goes through this, so that the
 * switch to database-sourced content later is safe by default.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Render an icon from the SVG sprite.
 *
 * @param string $name     Symbol id without the "i-" prefix.
 * @param string $modifier Optional extra class, e.g. 'icon--lg'.
 */
function icon(string $name, string $modifier = ''): string
{
    $class = trim('icon ' . $modifier);

    return sprintf(
        '<svg class="%s" aria-hidden="true" focusable="false"><use href="%s#i-%s"></use></svg>',
        e($class),
        e(url('assets/icons/sprite.svg')),
        e($name)
    );
}

/**
 * Named routes.
 *
 * Every cross-page link goes through here, so moving a page or introducing
 * pretty URLs later is a one-line change rather than a hunt through templates.
 */
function route(string $name): string
{
    $routes = [
        'home'          => url('index.php'),
        'seller.step1'  => url('pages/seller-intake-step-1.php'),
        'seller.step2'  => url('pages/seller-intake-step-2.php'),
        'seller.step3'  => url('pages/seller-intake-step-3.php'),
        'seller.done'   => url('pages/seller-intake-step-3.php') . '?state=submitted',
        'advisor.call'  => 'tel:+442079460892',
        'advisor.email' => 'mailto:desk@hsproperties.com',
    ];

    return $routes[$name] ?? url('index.php');
}

/**
 * Primary navigation.
 *
 * `key` is matched against $currentPage to mark the active item.
 */
function nav_items(): array
{
    return [
        ['key' => 'home',       'label' => 'Home',       'href' => url('index.php')],
        ['key' => 'properties', 'label' => 'Properties', 'href' => '#portfolio'],
        ['key' => 'services',   'label' => 'Services',   'href' => '#advisory'],
        ['key' => 'about',      'label' => 'About Us',   'href' => '#distinction'],
        ['key' => 'contact',    'label' => 'Contact',    'href' => '#contact'],
    ];
}

// Bootstrap backend database, session, and models
if (!defined('HS_BOOTSTRAPPED')) {
    define('HS_BOOTSTRAPPED', true);
    require_once APP_ROOT . '/backend/bootstrap.php';
}
