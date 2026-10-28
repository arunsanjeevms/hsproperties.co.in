<?php
/**
 * HS Properties — API: Contact Inquiry Endpoint.
 */

declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';
require_once __DIR__ . '/../backend/controllers/ContactController.php';

$controller = new ContactController();
$controller->handle();
