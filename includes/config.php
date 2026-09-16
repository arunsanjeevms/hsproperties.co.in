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
define('SITE_TAGLINE', 'Private Client Desk');

/**
 * Build a site-relative URL.
 */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
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
        'buyer.step1'   => url('pages/buyer-intake-step-1.php'),
        'buyer.step2'   => url('pages/buyer-intake-step-2.php'),
        'buyer.step3'   => url('pages/buyer-intake-step-3.php'),

        // Shared post-submission page; `?flow=` selects the mandate type.
        'intake.submitted' => url('pages/intake-submitted.php'),
        'seller.done'      => url('pages/intake-submitted.php') . '?flow=seller',
        'buyer.done'       => url('pages/intake-submitted.php') . '?flow=buyer',
        // Admin operations console.
        'admin.dashboard' => url('pages/admin/dashboard.php'),
        'admin.requests'  => url('pages/admin/requests.php'),
        'admin.request'   => url('pages/admin/request-detail.php'),
        'admin.properties'=> url('pages/admin/properties.php'),
        'admin.property'  => url('pages/admin/property-detail.php'),
        'admin.property.new' => url('pages/admin/property-new.php'),

        'contact.call'  => 'tel:+442079460892',
        'contact.email' => 'mailto:desk@hsproperties.com',
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
        ['key' => 'services',   'label' => 'Services',   'href' => '#mandate'],
        ['key' => 'about',      'label' => 'About Us',   'href' => '#distinction'],
        ['key' => 'contact',    'label' => 'Contact',    'href' => '#contact'],
    ];
}
