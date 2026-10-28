<?php
/**
 * Intake shell — document head, minimal header and progress rail.
 *
 * The intake flow deliberately uses a quieter chrome than the marketing site:
 * no primary navigation, no marketing CTAs, nothing that invites the user to
 * wander off mid-submission.
 *
 * Expects:
 *   $pageTitle    string
 *   $intakeStep   int     1-3, which step is current
 *   $intakeSteps  array   from intake_steps()
 *   $intakeFlow   string  label for the breadcrumb, e.g. 'Seller Intake'
 */

require_once __DIR__ . '/config.php';

$pageTitle   = $pageTitle ?? 'Private Intake — HS Properties';
$intakeStep  = $intakeStep ?? 1;
$intakeFlow  = $intakeFlow ?? 'Seller Intake';
$intakeSteps = $intakeSteps ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="Submit a property privately to the HS Properties advisory desk. Confidential intake, never publicly listed.">
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#faf6f1">

  <link rel="icon" type="image/svg+xml" href="<?= e(url('assets/images/logo.svg')) ?>">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
    rel="stylesheet">

  <link rel="stylesheet" href="<?= e(url('css/variables.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/variables.css') ?: '1' ?>">
  <link rel="stylesheet" href="<?= e(url('css/global.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/global.css') ?: '1' ?>">
  <link rel="stylesheet" href="<?= e(url('css/components.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/components.css') ?: '1' ?>">
  <link rel="stylesheet" href="<?= e(url('css/intake.css')) ?>?v=<?= @filemtime(__DIR__ . '/../css/intake.css') ?: '1' ?>">

  <script>document.documentElement.classList.add('has-js');</script>
</head>
<body class="intake-body">
  <a class="skip-link" href="#intake-form">Skip to the form</a>

  <header class="intake-header">
    <div class="container intake-header__inner">

      <a class="intake-brand" href="<?= e(route('home')) ?>">
        <span class="intake-brand__mark" aria-hidden="true">HS</span>
        <span>
          <span class="intake-brand__name">HS Properties</span>
          <span class="intake-brand__tagline">Private Real Estate Advisory</span>
        </span>
      </a>

      <div class="intake-header__aside">
        <span class="intake-header__note">Need confidential assistance?</span>
        <a class="intake-header__cta" href="<?= e(route('advisor.call')) ?>">
          Speak to an Advisor
          <?= icon('arrow-right', 'icon--sm') ?>
        </a>
      </div>

    </div>
  </header>

  <?php include __DIR__ . '/intake-progress.php'; ?>
