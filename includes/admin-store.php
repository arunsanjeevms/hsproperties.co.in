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
