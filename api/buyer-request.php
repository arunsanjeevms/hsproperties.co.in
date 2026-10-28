<?php
/**
 * HS Properties — API: Buyer Request Endpoint.
 */

declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';
require_once __DIR__ . '/../backend/controllers/BuyerRequestController.php';

$controller = new BuyerRequestController();
$controller->handle();
