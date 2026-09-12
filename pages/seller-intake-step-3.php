<?php
/**
 * HS Properties — Private Seller Intake, Step 03: Review & Submit.
 *
 * The final review of the submission. Submitting posts to the shared
 * Request Submitted & Review page (route `seller.done`).
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/seller-intake-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$pageTitle   = 'Private Seller Intake — Step 03: Review & Submit | HS Properties';
$intakeStep  = 3;
$intakeFlow  = 'Seller Intake';
$intakeSteps = intake_steps();

include __DIR__ . '/../includes/intake-header.php';
?>

<main class="intake-main">
  <div class="container intake-grid">

    <!-- ================= Editorial column ================= -->
    <aside class="intake-aside">
      <p class="intake-aside__eyebrow">Private Property Mandate</p>

      <h1 class="intake-aside__title">
        Review before
        <em>we proceed.</em>
      </h1>

      <p class="intake-aside__lede">
        Take a final look at the information you&rsquo;ve shared. Once
        submitted, our team will review the property and determine the
        appropriate next steps.
      </p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Privacy Protocol</h2>
        <p class="protocol__text">
          Your submission remains confidential and is reviewed by the
          HS Properties team.
        </p>
      </div>

      <ul class="assurances">
        <li class="assurances__item">Reviewed by our team</li>
        <li class="assurances__item">Documentation handled confidentially</li>
        <li class="assurances__item">No automatic public listing</li>
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
      action="<?= e(route('seller.done')) ?>">

      <div class="panel__head">
        <div>
          <h2 class="panel__title">Final Review</h2>
          <p class="panel__note">
            Confirm the details below before submitting your property for
            private evaluation.
          </p>
        </div>
        <span class="panel__tag">Step 03 of 03</span>
      </div>

      <?php foreach (seller_review_sections() as $i => $section): ?>
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

      <!-- Documentation -->
      <section class="fieldset">
        <div class="review__head">
          <h3 class="review__title">3. Documentation Available</h3>
          <a class="review__edit" href="<?= e(route('seller.step2')) ?>">
            Edit
            <span class="visually-hidden">documentation</span>
            <?= icon('arrow-right', 'icon--sm') ?>
          </a>
        </div>

        <ul class="files">
          <?php foreach (staged_documents() as $doc): ?>
            <?= document_row($doc, 'summary') ?>
          <?php endforeach; ?>
        </ul>
      </section>

      <!-- Confidentiality -->
      <div class="private-panel">
        <span class="private-panel__icon"><?= icon('lock', 'icon--sm') ?></span>
        <div>
          <h3 class="private-panel__title">Private by design.</h3>
          <p class="private-panel__text">
            Your submission is shared strictly with the HS Properties senior
            team for evaluation. It will not automatically become a
            public property listing or be syndicated across external portals.
          </p>
        </div>
      </div>

      <!-- Declaration -->
      <label class="declaration">
        <input class="option__input" type="checkbox" name="declaration" value="1" required>
        <span class="declaration__box" aria-hidden="true"><?= icon('check', 'icon--sm') ?></span>
        <span class="declaration__text">
          I confirm that the information provided is accurate to the best of
          my knowledge and that I am authorized to submit this property for
          evaluation.
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
          <a class="link-quiet" href="<?= e(route('seller.step2')) ?>">
            <?= icon('arrow-left', 'icon--sm') ?>
            Back to Step 02
          </a>
          <button class="btn-submit" type="submit">
            Submit Property for Review
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
