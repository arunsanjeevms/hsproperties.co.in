<?php
/**
 * Document head + fixed site header.
 *
 * Expects (optional): $pageTitle, $pageDescription, $currentPage.
 */

require_once __DIR__ . '/config.php';

$pageTitle = $pageTitle ?? 'HS Properties — Private Real Estate Advisory';
$pageDescription = $pageDescription ?? 'A private, curated approach to acquiring and presenting exceptional residential and estate assets across India\'s premier enclaves.';
$currentPage = $currentPage ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($pageDescription) ?>">
  <meta name="theme-color" content="#201612">

  <link rel="icon" type="image/svg+xml" href="<?= e(url('assets/images/logo.svg')) ?>">

  <?php /* Fonts: preconnect then a single combined request, swap-rendered. */ ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">

  <?php /* Load order matters: tokens, base, components, page, then overrides. */ ?>
  <link rel="stylesheet" href="<?= e(url('css/variables.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/variables.css') ?: '1' ?>">
  <link rel="stylesheet" href="<?= e(url('css/global.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/global.css') ?: '1' ?>">
  <link rel="stylesheet" href="<?= e(url('css/components.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/components.css') ?: '1' ?>">
  <link rel="stylesheet" href="<?= e(url('css/pages.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/pages.css') ?: '1' ?>">
  <link rel="stylesheet" href="<?= e(url('css/responsive.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/responsive.css') ?: '1' ?>">

  <?php /* Set synchronously, before first paint: scroll-reveal styles are
           scoped to .has-js so the page stays fully readable without JS. */ ?>
  <script>document.documentElement.classList.add('has-js');</script>
</head>
<body>
  <a class="skip-link" href="#main">Skip to main content</a>

  <header class="site-header" id="site-header">
    <div class="container site-header__inner">

      <a class="brand" href="<?= e(url('index.php')) ?>">
        <img
          class="brand__mark"
          src="<?= e(url('assets/images/logo.svg')) ?>"
          alt=""
          width="296" height="52">
        <span class="brand__text">
          <span class="brand__name"><?= e(SITE_NAME) ?></span>
          <span class="brand__tagline"><?= e(SITE_TAGLINE) ?></span>
        </span>
      </a>

      <?php include __DIR__ . '/navigation.php'; ?>

      <div class="header__actions">
        <a class="btn btn--ghost" href="#contact">Advisor Portal</a>
        <a class="btn btn--outline" href="<?= e(route('seller.step1')) ?>">Sell Your Property</a>
        <a class="btn btn--primary shimmer" href="#buyer">Find Your Property</a>

        <a class="avatar" href="#contact" aria-label="Account and advisor portal">
          <?= icon('user') ?>
        </a>

        <button
          class="nav-toggle"
          type="button"
          id="nav-toggle"
          aria-expanded="false"
          aria-controls="nav-drawer"
          aria-label="Open navigation menu">
          <?= icon('menu') ?>
        </button>
      </div>

    </div>
  </header>

  <?php /* Mobile drawer — mirrors the primary nav plus the key actions. */ ?>
  <div class="nav-drawer" id="nav-drawer" hidden>
    <div class="container">
      <ul class="nav-drawer__list">
        <?php foreach (nav_items() as $item): ?>
          <li>
            <a
              class="nav-drawer__link"
              href="<?= e($item['href']) ?>"
              <?= $currentPage === $item['key'] ? 'aria-current="page"' : '' ?>
            ><?= e($item['label']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="nav-drawer__actions">
        <a class="btn btn--outline" href="<?= e(route('seller.step1')) ?>">Sell Your Property</a>
        <a class="btn btn--primary" href="#buyer">Find Your Property</a>
      </div>
    </div>
  </div>
