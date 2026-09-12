<?php
/**
 * Admin console shell — head, persistent sidebar, topbar.
 *
 * Transcribed from the Stitch admin screens: gold accent rule, "Admin Console"
 * section header, the counted navigation list, the "Discreet Properties /
 * Meaningful Connections" plate, utility links, and the operator card. The rail
 * is persistent on desktop and a toggled drawer below 1024px.
 *
 * Expects:
 *   $pageTitle   string
 *   $adminPage   string  key matched against admin_nav()
 *   $adminCrumbs array   [['label' => string, 'href' => ?string], …]
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/admin-data.php';

$pageTitle   = $pageTitle ?? 'Admin Console — HS Properties';
$adminPage   = $adminPage ?? 'overview';
$adminCrumbs = $adminCrumbs ?? [];
$operator    = admin_operator();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#0c1019">

  <link rel="icon" type="image/svg+xml" href="<?= e(url('assets/images/logo.svg')) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="<?= e(url('css/variables.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('css/global.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('css/admin.css')) ?>">

  <script>document.documentElement.classList.add('has-js');</script>
</head>
<body class="console">
  <a class="skip-link" href="#console-main">Skip to the console</a>

  <!-- ================= Persistent sidebar ================= -->
  <aside class="rail" id="console-rail" aria-label="Console navigation">

    <div class="rail__top">
      <span class="rail__accent" aria-hidden="true"></span>
      <p class="rail__eyebrow">Admin Console</p>

      <nav>
        <ul class="rail__list">
          <?php foreach (admin_nav() as $item): ?>
            <?php
              $href = isset($item['route'])
                ? route($item['route']) . (isset($item['query']) ? '?' . $item['query'] : '')
                : null;
              $isCurrent = $item['key'] === $adminPage;
            ?>
            <li>
              <?php if ($href !== null): ?>
                <a
                  class="rail__link <?= $isCurrent ? 'is-current' : '' ?>"
                  href="<?= e($href) ?>"
                  <?= $isCurrent ? 'aria-current="page"' : '' ?>>
                  <?= icon($item['icon'], 'icon--sm rail__icon') ?>
                  <span class="rail__label"><?= e($item['label']) ?></span>
                  <?php if (isset($item['count'])): ?>
                    <span class="rail__count"><?= e((string) $item['count']) ?></span>
                  <?php endif; ?>
                </a>
              <?php else: ?>
                <span class="rail__link is-disabled" aria-disabled="true">
                  <?= icon($item['icon'], 'icon--sm rail__icon') ?>
                  <span class="rail__label"><?= e($item['label']) ?></span>
                  <?php if (isset($item['count'])): ?>
                    <span class="rail__count"><?= e((string) $item['count']) ?></span>
                  <?php endif; ?>
                </span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    </div>

    <div class="rail__bottom">
      <div class="rail__plate">
        <span class="rail__plate-title">Discreet Properties</span>
        <span class="rail__plate-note">Meaningful Connections</span>
      </div>

      <ul class="rail__list rail__list--utility">
        <?php foreach (admin_nav_utility() as $item): ?>
          <li>
            <span class="rail__link is-disabled" aria-disabled="true">
              <?= icon($item['icon'], 'icon--sm rail__icon') ?>
              <span class="rail__label"><?= e($item['label']) ?></span>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="rail__operator">
        <span class="rail__avatar" aria-hidden="true"><?= e($operator['initials']) ?></span>
        <span>
          <span class="rail__operator-name"><?= e($operator['name']) ?></span>
          <span class="rail__operator-role"><?= e($operator['role']) ?></span>
        </span>
      </div>
    </div>

  </aside>

  <div class="console-scrim" id="console-scrim" hidden></div>

  <!-- ================= Content column ================= -->
  <div class="console-body">

    <header class="topbar">
      <div class="topbar__lead">
        <button
          class="topbar__burger"
          type="button"
          id="console-rail-toggle"
          aria-controls="console-rail"
          aria-expanded="false">
          <?= icon('menu', 'icon--sm') ?>
          <span class="visually-hidden">Toggle navigation</span>
        </button>

        <ol class="topbar__crumb">
          <?php foreach ($adminCrumbs as $i => $crumb): ?>
            <?php if ($i > 0): ?>
              <li class="topbar__sep" aria-hidden="true">/</li>
            <?php endif; ?>
            <li class="topbar__crumb-item <?= $i === count($adminCrumbs) - 1 ? 'is-current' : '' ?>">
              <?php if (!empty($crumb['href'])): ?>
                <a href="<?= e($crumb['href']) ?>"><?= e($crumb['label']) ?></a>
              <?php else: ?>
                <?= e($crumb['label']) ?>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ol>
      </div>

      <div class="topbar__aside">
        <button class="topbar__search" type="button">
          <?= icon('filter', 'icon--sm') ?>
          <span class="topbar__search-text">Search the registry</span>
          <kbd class="topbar__kbd">&#8984;K</kbd>
        </button>

        <span class="topbar__avatar" aria-label="<?= e($operator['name']) ?>">
          <?= e($operator['initials']) ?>
        </span>
      </div>
    </header>

    <main class="console-main" id="console-main">
