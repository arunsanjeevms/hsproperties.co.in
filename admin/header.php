<?php
/**
 * HS Properties — Admin Header.
 */

declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';
Auth::requireAdmin();

$activePage = $activePage ?? 'dashboard';
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($adminTitle ?? 'Admin Advisory Console') ?> — HS Properties</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('css/variables.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('css/admin.css')) ?>">
</head>
<body class="admin-body">

  <aside class="admin-sidebar">
    <div class="admin-sidebar__brand">
      <a href="<?= e(url('admin/index.php')) ?>" class="admin-sidebar__logo" style="display: flex; align-items: center; gap: 10px;">
        <img src="<?= e(url('assets/images/logo.svg')) ?>" alt="" style="height: 24px; width: auto;">
        <span>HS <span>PROPERTIES</span></span>
      </a>
      <span class="admin-sidebar__badge">Desk</span>
    </div>

    <nav class="admin-sidebar__nav">
      <a href="<?= e(url('admin/index.php')) ?>" class="admin-nav__item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
        <span>📊</span> <span>Dashboard</span>
      </a>
      <a href="<?= e(url('admin/requests.php')) ?>" class="admin-nav__item <?= $activePage === 'seller_requests' ? 'active' : '' ?>">
        <span>📑</span> <span>Seller Mandates</span>
      </a>
      <a href="<?= e(url('admin/buyer-requests.php')) ?>" class="admin-nav__item <?= $activePage === 'buyer_requests' ? 'active' : '' ?>">
        <span>🎯</span> <span>Buyer Mandates</span>
      </a>
      <a href="<?= e(url('admin/properties.php')) ?>" class="admin-nav__item <?= $activePage === 'properties' ? 'active' : '' ?>">
        <span>🏛️</span> <span>Properties</span>
      </a>
      <a href="<?= e(url('admin/messages.php')) ?>" class="admin-nav__item <?= $activePage === 'messages' ? 'active' : '' ?>">
        <span>✉️</span> <span>Messages</span>
      </a>
      <a href="<?= e(url('index.php')) ?>" target="_blank" class="admin-nav__item">
        <span>🌐</span> <span>View Website</span>
      </a>
    </nav>

    <div class="admin-sidebar__footer">
      <div class="admin-sidebar__user">
        <span class="admin-sidebar__user-name"><?= e($user['name'] ?? 'Senior Partner') ?></span>
        <span><?= e($user['email'] ?? 'admin@hsproperties.com') ?></span>
      </div>
      <a href="<?= e(url('admin/logout.php')) ?>" class="btn-admin btn-admin--sm btn-admin--secondary" title="Sign Out">Exit</a>
    </div>
  </aside>

  <div class="admin-layout">
    <header class="admin-topbar">
      <h1 class="admin-topbar__title"><?= e($adminTitle ?? 'Advisory Console') ?></h1>
      <div class="admin-topbar__actions">
        <a href="<?= e(url('admin/properties.php?action=create')) ?>" class="btn-admin btn-admin--primary">
          + Add Property
        </a>
      </div>
    </header>

    <main class="admin-content">
      <?php if ($msg = Session::flash('success')): ?>
        <div class="admin-alert admin-alert--success">
          <span><?= e($msg) ?></span>
        </div>
      <?php endif; ?>

      <?php if ($err = Session::flash('error')): ?>
        <div class="admin-alert admin-alert--error">
          <span><?= e($err) ?></span>
        </div>
      <?php endif; ?>
