<?php
/**
 * Intake progress rail — breadcrumb plus the three-stage indicator.
 *
 * Driven entirely by $intakeStep, so each page states its step number once
 * and the completed / current / upcoming states follow automatically.
 *
 * Expects: $intakeStep (int), $intakeSteps (array), $intakeFlow (string).
 */

$intakeStep  = $intakeStep ?? 1;
$intakeFlow  = $intakeFlow ?? 'Seller Intake';
$intakeSteps = $intakeSteps ?? [];
?>
<nav class="intake-progress" aria-label="Intake progress">
  <div class="container intake-progress__inner">

    <ol class="crumb">
      <li class="crumb__item crumb__item--strong">Confidential Mandate</li>
      <li class="crumb__sep" aria-hidden="true">/</li>
      <li class="crumb__item"><?= e($intakeFlow) ?></li>
      <li class="crumb__sep" aria-hidden="true">/</li>
      <li class="crumb__item crumb__item--current">
        Step <?= e(str_pad((string) $intakeStep, 2, '0', STR_PAD_LEFT)) ?>
      </li>
    </ol>

    <ol class="steps">
      <?php foreach ($intakeSteps as $i => $step): ?>
        <?php
          $number = $i + 1;
          $state  = $number < $intakeStep ? 'done'
                  : ($number === $intakeStep ? 'current' : 'todo');
        ?>
        <?php if ($i > 0): ?>
          <li class="steps__rule" aria-hidden="true"></li>
        <?php endif; ?>

        <li
          class="steps__item steps__item--<?= e($state) ?>"
          <?= $state === 'current' ? 'aria-current="step"' : '' ?>
        >
          <span class="steps__dot" aria-hidden="true"></span>
          <?= e(str_pad((string) $number, 2, '0', STR_PAD_LEFT)) ?>
          <?= e($step['label']) ?>
          <?php if ($state === 'done'): ?>
            <span class="visually-hidden">(completed)</span>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ol>

  </div>
</nav>
