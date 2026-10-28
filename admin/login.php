<?php
/**
 * HS Properties — Admin Authentication Login.
 */

declare(strict_types=1);

require_once __DIR__ . '/../backend/bootstrap.php';
require_once __DIR__ . '/../backend/controllers/AdminController.php';

if (Auth::check()) {
    redirect(url('admin/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AdminController();
    $controller->login();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Private Console Login — HS Properties</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(url('css/variables.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('css/admin.css')) ?>">
</head>
<body class="admin-body">

<div class="login-wrap">
  <div class="login-card">
    <div class="login-header">
      <div class="login-logo" style="display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 8px;">
        <img src="<?= e(url('assets/images/logo.svg')) ?>" alt="" style="height: 36px; width: auto;">
        <span>HS PROPERTIES</span>
      </div>
      <p class="login-sub">Confidential Advisory Console</p>
    </div>

    <?php if ($err = Session::flash('error')): ?>
      <div class="admin-alert admin-alert--error">
        <span><?= e($err) ?></span>
      </div>
    <?php endif; ?>

    <form method="post" action="<?= e(url('admin/login.php')) ?>">
      <div class="admin-form-group">
        <label for="email">Advisory Email</label>
        <input type="email" id="email" name="email" class="admin-input" required autofocus placeholder="admin@hsproperties.com" value="admin@hsproperties.com">
      </div>

      <div class="admin-form-group">
        <label for="password">Password Key</label>
        <input type="password" id="password" name="password" class="admin-input" required placeholder="••••••••" value="changeme123">
      </div>

      <div style="margin-top: 24px;">
        <button type="submit" class="btn-admin btn-admin--primary" style="width: 100%; justify-content: center; padding: 12px;">
          Authenticate Entry
        </button>
      </div>

      <p style="text-align: center; margin-top: 20px; font-size: 0.75rem; color: #8d7b70;">
        Authorized private advisory personnel only. All access is logged with IP verification.
      </p>
    </form>
  </div>
</div>

</body>
</html>
