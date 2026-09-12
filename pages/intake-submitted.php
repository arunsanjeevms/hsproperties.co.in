<?php
/**
 * HS Properties — Request Submitted & Review.
 *
 * Shared by both intakes: `?flow=buyer` (default) or `?flow=seller`. Every
 * difference between the two lives in submission_view(), so this template has
 * no flow conditionals and a third mandate type would be a data change only.
 *
 * Frontend stage: reached directly by the Step 03 form action. When the backend
 * lands, the controller redirects here with the created reference instead.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/intake-submitted-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$flow = submission_flow($_GET['flow'] ?? null);
$view = submission_view($flow);

$pageTitle   = 'Private ' . ($flow === 'seller' ? 'Seller' : 'Buyer')
             . ' Intake — Request Submitted & Review | HS Properties';
$intakeStep  = 4;
$intakeFlow  = $view['flow_label'];
$intakeSteps = submission_steps($flow);

include __DIR__ . '/../includes/intake-header.php';
?>

<main class="intake-main">
  <div class="container intake-grid">

    <!-- ================= Editorial column ================= -->
    <aside class="intake-aside">
      <p class="intake-aside__eyebrow"><?= e($view['eyebrow']) ?></p>

      <h1 class="intake-aside__title">
        <?= e($view['aside_title']) ?>
        <em><?= e($view['aside_accent']) ?></em>
      </h1>

      <p class="intake-aside__lede"><?= e($view['aside_lede']) ?></p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Privacy Protocol</h2>
        <p class="protocol__text"><?= e($view['protocol']) ?></p>
      </div>

      <ul class="assurances">
        <?php foreach ($view['assurances'] as $assurance): ?>
          <li class="assurances__item"><?= e($assurance) ?></li>
        <?php endforeach; ?>
      </ul>

      <p class="phase">
        <span class="phase__number">04</span>
        <span class="phase__total">/04</span>
        <span class="phase__label"><?= e($view['phase']) ?></span>
      </p>
    </aside>

    <!-- ================= Confirmation column ================= -->
    <section class="panel" id="intake-form" aria-labelledby="confirm-title">

      <p class="confirm__eyebrow">
        <?= icon('verified', 'icon--sm') ?>
        Submission Received
      </p>

      <h2 class="confirm__title" id="confirm-title">
        <?= e($view['title']) ?>
        <em><?= e($view['title_accent']) ?></em>
      </h2>

      <p class="confirm__text"><?= $view['text'] ?></p>

      <dl class="reference">
        <?php foreach ($view['reference'] as $ref): ?>
          <div>
            <dt class="reference__label"><?= e($ref['label']) ?></dt>
            <dd class="reference__value <?= !empty($ref['accent']) ? 'reference__value--accent' : '' ?>">
              <?= e($ref['value']) ?>
            </dd>
          </div>
        <?php endforeach; ?>
      </dl>

      <!-- Review pipeline -->
      <section class="fieldset">
        <div class="review__head">
          <h3 class="review__title">1. Review Progress</h3>
          <span class="panel__tag">Live Status</span>
        </div>

        <dl class="review__list">
          <?php foreach ($view['stages'] as $stage): ?>
            <div class="review__row">
              <dt class="review__label"><?= e($stage['label']) ?></dt>
              <dd class="review__value">
                <?= e($stage['value']) ?>
                <?php if ($stage['state'] === 'current'): ?>
                  <span class="pip" aria-hidden="true"></span>
                  <span class="visually-hidden">(current stage)</span>
                <?php elseif ($stage['state'] === 'done'): ?>
                  <span class="visually-hidden">(completed)</span>
                <?php endif; ?>
              </dd>
            </div>
          <?php endforeach; ?>
        </dl>

        <p class="note note--plain">
          Stages advance as the team works through the mandate. You will be
          contacted directly &mdash; there is nothing to monitor here.
        </p>
      </section>

      <!-- Assigned desk -->
      <section class="fieldset">
        <div class="review__head">
          <h3 class="review__title">2. Your Client Desk</h3>
          <a class="review__edit" href="<?= e(route('contact.call')) ?>">
            Speak to Our Team
            <?= icon('arrow-right', 'icon--sm') ?>
          </a>
        </div>

        <dl class="review__list">
          <?php foreach ($view['desk'] as $row): ?>
            <div class="review__row">
              <dt class="review__label"><?= e($row['label']) ?></dt>
              <dd class="review__value"><?= e($row['value']) ?></dd>
            </div>
          <?php endforeach; ?>
        </dl>

        <p class="note">
          <?= icon('contact-phone', 'icon--sm') ?>
          Quote your reference number on any correspondence so the desk can
          retrieve the mandate without disclosing details over an open channel.
        </p>
      </section>

      <!-- Submission summary -->
      <section class="fieldset">
        <div class="review__head">
          <h3 class="review__title">3. Submitted Summary</h3>
          <a class="review__edit" href="<?= e(route($view['edit_route'])) ?>">
            Review submission
            <?= icon('arrow-right', 'icon--sm') ?>
          </a>
        </div>

        <?php foreach ($view['summary'] as $section): ?>
          <p class="group-label"><?= e($section['title']) ?></p>
          <dl class="review__list">
            <?php foreach ($section['rows'] as $row): ?>
              <div class="review__row">
                <dt class="review__label"><?= e($row['label']) ?></dt>
                <dd class="review__value">
                  <?= e($row['value']) ?>
                  <?php if (!empty($row['muted'])): ?>
                    <span class="muted"><?= e($row['muted']) ?></span>
                  <?php endif; ?>
                </dd>
              </div>
            <?php endforeach; ?>
          </dl>
        <?php endforeach; ?>
      </section>

      <!-- Confidentiality -->
      <div class="private-panel">
        <span class="private-panel__icon"><?= icon('shield', 'icon--sm') ?></span>
        <div>
          <h3 class="private-panel__title">Strict Confidentiality Enforced</h3>
          <p class="private-panel__text">
            Your submission is archived in an encrypted registry. No external
            notifications or broker syndications are triggered, and no part of
            this mandate is published without your written instruction.
          </p>
        </div>
      </div>

      <div class="confirm__actions">
        <a class="link-quiet" href="<?= e(route($view['restart'])) ?>">
          <?= icon('arrow-left', 'icon--sm') ?>
          <?= e($view['restart_label']) ?>
        </a>

        <div class="form-foot__actions">
          <a class="link-quiet" href="<?= e(route('contact.email')) ?>">
            Email the Desk
            <?= icon('arrow-right', 'icon--sm') ?>
          </a>
          <a class="btn-submit" href="<?= e(route('home')) ?>">
            Return to HS Properties
            <?= icon('arrow-right', 'icon--sm') ?>
          </a>
        </div>
      </div>

    </section>

  </div>
</main>

<?php include __DIR__ . '/../includes/intake-footer.php'; ?>
