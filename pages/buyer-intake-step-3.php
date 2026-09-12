<?php
/**
 * HS Properties — Private Buyer Intake, Step 03: Review & Submit.
 *
 * The final review of the brief. Submitting posts to the shared
 * Request Submitted & Review page (route `buyer.done`).
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/buyer-intake-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$pageTitle   = 'Private Buyer Intake — Step 03: Review & Submit | HS Properties';
$intakeStep  = 3;
$intakeFlow  = 'Buyer Intake';
$intakeSteps = buyer_intake_steps();

include __DIR__ . '/../includes/intake-header.php';
?>

<main class="intake-main">
  <div class="container intake-grid">

    <!-- ================= Editorial column ================= -->
    <aside class="intake-aside">
      <p class="intake-aside__eyebrow">Private Acquisition Mandate</p>

      <h1 class="intake-aside__title">
        Review before
        <em>we proceed.</em>
      </h1>

      <p class="intake-aside__lede">
        Take a final look at the brief you&rsquo;ve shared. Once submitted, our
        team will qualify the mandate and begin matching it against
        off-market properties.
      </p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Privacy Protocol</h2>
        <p class="protocol__text">
          Your brief remains confidential and is reviewed by the HS Properties
          team alone.
        </p>
      </div>

      <ul class="assurances">
        <li class="assurances__item">Brief reviewed by our team</li>
        <li class="assurances__item">Budget held confidentially</li>
        <li class="assurances__item">No circulation to broker networks</li>
      </ul>

      <p class="phase">
        <span class="phase__number">03</span>
        <span class="phase__total">/03</span>
        <span class="phase__label">Phase: Review &amp; Submission</span>
      </p>
    </aside>

    <!-- ================= View A: final review ================= -->
    <form
      class="panel"
      id="intake-form"
      method="post"
      action="<?= e(route('buyer.done')) ?>">

      <div class="panel__head">
        <div>
          <h2 class="panel__title">Final Review</h2>
          <p class="panel__note">
            Confirm the details below before submitting your acquisition brief
            for private matching.
          </p>
        </div>
        <span class="panel__tag">Step 03 of 03</span>
      </div>

      <?php foreach (buyer_review_sections() as $i => $section): ?>
        <section class="fieldset">
          <div class="review__head">
            <h3 class="review__title">
              <?= e((string) ($i + 1)) ?>. <?= e($section['title']) ?>
            </h3>
            <a class="review__edit" href="<?= e($section['edit_url']) ?>">
              Edit
              <span class="visually-hidden"><?= e($section['title']) ?></span>
              <?= icon('arrow-right', 'icon--sm') ?>
            </a>
          </div>

          <dl class="review__list">
            <?php foreach ($section['rows'] as $row): ?>
              <div class="review__row">
                <dt class="review__label"><?= e($row['label']) ?></dt>
                <dd class="review__value">
                  <?= e($row['value']) ?>
                  <?php if (!empty($row['muted'])): ?>
                    <span class="muted"><?= e($row['muted']) ?></span>
                  <?php endif; ?>
                  <?php if (!empty($row['accent'])): ?>
                    <span class="pip" aria-hidden="true"></span>
                  <?php endif; ?>
                </dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </section>
      <?php endforeach; ?>

      <!-- Priorities -->
      <section class="fieldset">
        <div class="review__head">
          <h3 class="review__title">3. Non-Negotiable Priorities</h3>
          <a class="review__edit" href="<?= e(route('buyer.step2')) ?>">
            Edit
            <span class="visually-hidden">priorities</span>
            <?= icon('arrow-right', 'icon--sm') ?>
          </a>
        </div>

        <dl class="review__list">
          <?php foreach (buyer_priority_summary() as $priority): ?>
            <div class="review__row">
              <dt class="review__label"><?= e($priority['label']) ?></dt>
              <dd class="review__value"><?= e($priority['status']) ?></dd>
            </div>
          <?php endforeach; ?>
        </dl>
      </section>

      <!-- Confidentiality -->
      <div class="private-panel">
        <span class="private-panel__icon"><?= icon('lock', 'icon--sm') ?></span>
        <div>
          <h3 class="private-panel__title">Private by design.</h3>
          <p class="private-panel__text">
            Your brief is shared strictly with the HS Properties senior
            team. Your identity and budget are never disclosed to a
            seller or syndicated across external portals without your written
            instruction.
          </p>
        </div>
      </div>

      <!-- Declaration -->
      <label class="declaration">
        <input class="option__input" type="checkbox" name="declaration" value="1" required>
        <span class="declaration__box" aria-hidden="true"><?= icon('check', 'icon--sm') ?></span>
        <span class="declaration__text">
          I confirm that the information provided is accurate to the best of
          my knowledge and that I am authorized to submit this acquisition
          brief for evaluation.
        </span>
      </label>

      <!-- Actions -->
      <div class="form-foot">
        <p class="form-foot__status">
          <strong>Step 3 of 3</strong>
          <span aria-hidden="true">&bull;</span>
          Final Submission
        </p>

        <div class="form-foot__actions">
          <a class="link-quiet" href="<?= e(route('buyer.step2')) ?>">
            <?= icon('arrow-left', 'icon--sm') ?>
            Back to Step 02
          </a>
          <button class="btn-submit" type="submit">
            Submit Acquisition Brief
            <?= icon('arrow-right', 'icon--sm') ?>
          </button>
        </div>

        <p class="form-foot__aside">
          The HS Properties team may contact you after the initial review.
        </p>
      </div>

    </form>

  </div>
</main>

<?php include __DIR__ . '/../includes/intake-footer.php'; ?>
