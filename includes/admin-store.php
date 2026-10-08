<?php
/**
 * HS Properties — Admin store.
 *
 * The rest of the console is frontend-only: every list is a PHP array and
 * nothing is written anywhere. "Add a property" cannot work that way — a form
 * whose entries vanish is worse than no form — so properties added through the
 * console are appended to a small JSON file.
 *
 * This is a stand-in for the real database, not a substitute for it. It takes a
 * lock on write, but it has no users, no permissions and no history, so it
 * should be replaced by the real table before anyone depends on it. Each
 * function below is the shape of the query that will replace it.
 */

declare(strict_types=1);

/** Where added properties are kept. */
function admin_store_path(): string
{
    return dirname(__DIR__) . '/data/properties.json';
}

/**
 * Properties added through the console, newest first.
 *
 * Any read problem — missing file, unreadable JSON, a hand-edit that broke the
 * shape — returns an empty list rather than an error, so a damaged store can
 * never take the console down. The seeded properties still render.
 */
function admin_stored_properties(): array
{
    $path = admin_store_path();

    if (!is_readable($path)) {
        return [];
    }

    $raw = file_get_contents($path);

    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $rows = json_decode($raw, true);

    if (!is_array($rows)) {
        return [];
    }

    // Drop anything that is not a property-shaped row.
    $rows = array_values(array_filter($rows, function ($row) {
        return is_array($row) && isset($row['code'], $row['name']);
    }));

    return array_reverse($rows);
}

/**
 * Append one property to the store.
 *
 * Returns false rather than throwing when the directory cannot be written, so
 * the form can say so plainly instead of showing a PHP error.
 */
function admin_store_property(array $property): bool
{
    $path = admin_store_path();
    $dir  = dirname($path);

    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $handle = fopen($path, 'c+');

    if ($handle === false) {
        return false;
    }

    // Read, append and write under one exclusive lock: two operators pressing
    // Save at the same moment must not lose one another's property.
    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return false;
    }

    $raw  = stream_get_contents($handle);
    $rows = $raw === false || trim((string) $raw) === '' ? [] : json_decode((string) $raw, true);

    if (!is_array($rows)) {
        $rows = [];
    }

    $rows[] = $property;

    $json = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        flock($handle, LOCK_UN);
        fclose($handle);
        return false;
    }

    rewind($handle);
    ftruncate($handle, 0);
    $written = fwrite($handle, $json . "\n");

    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $written !== false;
}

/* ==========================================================================
   PROPERTY STATUS — the operator's manual override

   A property's status changes constantly and for reasons no rule can guess:
   the owner rings up, a buyer withdraws, the papers come back. So an operator
   sets it by hand, and the choice has to survive the page reload or the
   control is a lie.

   Overrides live in their own file keyed by property code, so one record can
   cover both the seeded properties and those added through the console
   without rewriting either.
   ========================================================================== */

/** Where manual status changes are kept. */
function admin_status_store_path(): string
{
    return dirname(__DIR__) . '/data/property-status.json';
}

/**
 * Manual status changes, as code => status.
 *
 * Like the property store, any read problem returns an empty list rather than
 * an error: a damaged override file must never take the registry down, it
 * should only mean the properties show their original status.
 */
function admin_status_overrides(): array
{
    $path = admin_status_store_path();

    if (!is_readable($path)) {
        return [];
    }

    $raw = file_get_contents($path);

    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $rows = json_decode($raw, true);

    if (!is_array($rows)) {
        return [];
    }

    $clean = [];

    foreach ($rows as $code => $row) {
        $status = is_array($row) ? ($row['status'] ?? null) : $row;

        if (is_string($code) && is_string($status) && isset(admin_property_statuses()[$status])) {
            $clean[$code] = $status;
        }
    }

    return $clean;
}

/** What is known about the last manual change to one property, if any. */
function admin_status_override_meta(string $code): ?array
{
    $path = admin_status_store_path();

    if (!is_readable($path)) {
        return null;
    }

    $rows = json_decode((string) file_get_contents($path), true);

    if (!is_array($rows) || !isset($rows[$code]) || !is_array($rows[$code])) {
        return null;
    }

    return $rows[$code];
}

/**
 * Record a manual status change.
 *
 * Returns false rather than throwing when the store cannot be written, so the
 * page can say so plainly instead of claiming a change that did not happen.
 * An unknown status or an unknown property code is refused outright: the
 * select is the only intended way in, and nothing else should get through.
 */
function admin_set_property_status(string $code, string $status, string $by = 'Admin'): bool
{
    if (!isset(admin_property_statuses()[$status])) {
        return false;
    }

    if (admin_property($code) === null) {
        return false;
    }

    $path = admin_status_store_path();
    $dir  = dirname($path);

    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $handle = fopen($path, 'c+');

    if ($handle === false) {
        return false;
    }

    // Read, change and write under one lock, as the property store does: two
    // operators changing two properties at once must not lose one another.
    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return false;
    }

    $raw  = stream_get_contents($handle);
    $rows = $raw === false || trim((string) $raw) === '' ? [] : json_decode((string) $raw, true);

    if (!is_array($rows)) {
        $rows = [];
    }

    $rows[$code] = [
        'status'  => $status,
        'by'      => $by,
        'changed' => date('d M Y'),
    ];

    $json = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        flock($handle, LOCK_UN);
        fclose($handle);
        return false;
    }

    rewind($handle);
    ftruncate($handle, 0);
    $written = fwrite($handle, $json . "\n");

    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return $written !== false;
}

/**
 * The next free property code.
 *
 * Codes are read aloud on the phone, so they stay short and sequential rather
 * than becoming random identifiers.
 */
function admin_next_property_code(): string
{
    $highest = 0;

    foreach (admin_properties() as $property) {
        if (preg_match('/^HS-P-(\d+)$/', (string) $property['code'], $m) === 1) {
            $highest = max($highest, (int) $m[1]);
        }
    }

    return 'HS-P-' . str_pad((string) ($highest + 1), 4, '0', STR_PAD_LEFT);
}
