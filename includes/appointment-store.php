<?php
/**
 * HS Properties — Appointment store.
 *
 * The same stand-in for the real database that admin-store.php is: a small
 * JSON file, written under an exclusive lock, with no users, no permissions
 * and no history. It should be replaced by the real table before anyone
 * depends on it, and each function below is the shape of the query that will
 * replace it.
 *
 * Unlike the property store this file is a map keyed by reference rather than
 * a list, because an appointment is edited as well as created: marking one
 * done is the same write as booking it. That also means a seeded appointment
 * can be completed without a second overrides file — the whole row is simply
 * written back with its new status.
 */

declare(strict_types=1);

/** Where appointments are kept. */
function admin_appointment_store_path(): string
{
    return dirname(__DIR__) . '/data/appointments.json';
}

/**
 * Appointments written through the console, as reference => row.
 *
 * Any read problem — missing file, unreadable JSON, a hand-edit that broke the
 * shape — returns an empty list rather than an error, so a damaged store can
 * never take the console down. The seeded appointments still render.
 */
function admin_stored_appointments(): array
{
    $path = admin_appointment_store_path();

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

    foreach ($rows as $ref => $row) {
        if (is_string($ref) && is_array($row) && isset($row['ref'], $row['kind'], $row['date'])) {
            $clean[$ref] = $row;
        }
    }

    return $clean;
}

/**
 * Write one appointment, creating it or replacing it.
 *
 * Returns false rather than throwing when the store cannot be written, so the
 * page can say so plainly instead of claiming a booking that did not happen.
 */
function admin_save_appointment(array $row): bool
{
    if (!isset($row['ref']) || !is_string($row['ref']) || $row['ref'] === '') {
        return false;
    }

    $path = admin_appointment_store_path();
    $dir  = dirname($path);

    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $handle = fopen($path, 'c+');

    if ($handle === false) {
        return false;
    }

    // Read, change and write under one lock: two operators booking at the same
    // moment must not lose one another's appointment.
    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return false;
    }

    $raw  = stream_get_contents($handle);
    $rows = $raw === false || trim((string) $raw) === '' ? [] : json_decode((string) $raw, true);

    if (!is_array($rows)) {
        $rows = [];
    }

    $rows[$row['ref']] = $row;

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
 * The next free appointment reference.
 *
 * Read aloud on the phone like a property code, so it stays short and
 * sequential rather than becoming a random identifier.
 */
function admin_next_appointment_ref(): string
{
    $highest = 0;

    foreach (admin_appointments() as $appointment) {
        if (preg_match('/^HS-A-(\d+)$/', (string) $appointment['ref'], $m) === 1) {
            $highest = max($highest, (int) $m[1]);
        }
    }

    return 'HS-A-' . str_pad((string) ($highest + 1), 4, '0', STR_PAD_LEFT);
}
