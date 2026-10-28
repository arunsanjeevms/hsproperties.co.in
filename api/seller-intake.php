<?php
/**
 * HS Properties — API: Seller Intake Endpoint.
 */

declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';
require_once __DIR__ . '/../backend/controllers/SellerIntakeController.php';

$controller = new SellerIntakeController();
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

if ($step === 1) {
    $controller->handleStep1();
} elseif ($step === 2) {
    $controller->handleStep2();
} elseif ($step === 3) {
    $controller->handleStep3();
} else {
    redirect(route('seller.step1'));
}
