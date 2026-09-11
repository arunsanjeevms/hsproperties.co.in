<?php
/**
 * HS Properties — Private Seller Intake, Step 01: Property.
 *
 * Frontend stage: the form posts straight to Step 02, which simply renders.
 * When the backend lands, that action points at a controller that persists the
 * step and redirects — the markup does not need to change.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/seller-intake-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$pageTitle   = 'Private Seller Intake — Step 01: Property | HS Properties';
$intakeStep  = 1;
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
        Tell us about the
        <em>property.</em>
      </h1>

      <p class="intake-aside__lede">
        Start with the essentials. We&rsquo;ll use these details to understand
        the property before discussing valuation, documentation, and discreet
        buyer positioning.
      </p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Advisory Protocol</h2>
        <p class="protocol__text">
          Your information is handled confidentially. Submissions are never
          listed publicly or broadcast across portals.
        </p>
      </div>

      <ul class="assurances">
        <li class="assurances__item">Direct oversight by a dedicated Senior Managing Partner.</li>
        <li class="assurances__item">Encrypted intake &amp; strict pre-NDA property screening.</li>
        <li class="assurances__item">Access restricted solely to pre-qualified high-net-worth buyers.</li>
      </ul>

      <p class="phase">
        <span class="phase__number">01</span>
        <span class="phase__total">/03</span>
        <span class="phase__label">Phase: Discovery</span>
      </p>
    </aside>

    <!-- ================= Form column ================= -->
    <form
      class="panel"
      id="intake-form"
      method="post"
      action="<?= e(route('seller.step2')) ?>">

      <div class="panel__head">
        <div>
          <h2 class="panel__title">Property Basics</h2>
          <p class="panel__note">
            Begin with the fundamentals of the property you&rsquo;re considering selling.
          </p>
        </div>
        <span class="panel__tag">Confidential Intake</span>
      </div>

      <!-- 1. Property type -->
      <div class="fieldset" role="group" aria-labelledby="fs1-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs1-label">
            1. Property Type<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Select single archetype</span>
        </div>

        <div class="options options--3">
          <?php foreach (property_types() as $type): ?>
            <?= option_card('property_type', $type, $type['value'] === 'villa') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 2. Property status -->
      <div class="fieldset" role="group" aria-labelledby="fs2-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs2-label">
            2. Property Status<span class="req" aria-hidden="true">*</span>
          </span>
        </div>

        <div class="options options--3">
          <?php foreach (property_statuses() as $status): ?>
            <label class="segment">
              <input
                class="option__input"
                type="radio"
                name="property_status"
                value="<?= e($status['value']) ?>"
                <?= $status['value'] === 'ready' ? 'checked' : '' ?>>
              <?= e($status['label']) ?>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 3. Location -->
      <div class="fieldset" role="group" aria-labelledby="fs3-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs3-label">
            3. Property Location<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Confidential Registry</span>
        </div>

        <div class="grid-3">
          <p class="input-group">
            <label class="input-group__label" for="city">City</label>
            <input class="input" type="text" id="city" name="city" placeholder="Bengaluru" required>
          </p>
          <p class="input-group">
            <label class="input-group__label" for="locality">Locality / Micro-market</label>
            <input class="input" type="text" id="locality" name="locality" placeholder="Whitefield" required>
          </p>
          <p class="input-group">
            <label class="input-group__label" for="pincode">PIN Code</label>
            <input
              class="input" type="text" id="pincode" name="pincode"
              placeholder="560066" inputmode="numeric" pattern="[0-9]{6}"
              autocomplete="postal-code" required>
          </p>
        </div>

        <p class="note">
          <?= icon('lock', 'icon--sm') ?>
          Specific location details help us understand the property&rsquo;s
          micro-market context. Exact door numbers are never published.
        </p>
      </div>

      <!-- 4. Size -->
      <div class="fieldset" role="group" aria-labelledby="fs4-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs4-label">
            4. Approximate Property Size<span class="req" aria-hidden="true">*</span>
          </span>
        </div>

        <div class="grid-2">
          <p class="input-group">
            <label class="input-group__label" for="extent">Extent / Built-up Area</label>
            <span class="affix affix--trail">
              <input
                class="input" type="text" id="extent" name="extent"
                placeholder="8200" inputmode="numeric" required>
              <span class="affix__trail" aria-hidden="true">approx.</span>
            </span>
          </p>
          <p class="input-group">
            <label class="input-group__label" for="unit">Unit of Measurement</label>
            <select class="input" id="unit" name="unit">
              <?php foreach (measurement_units() as $u): ?>
                <option value="<?= e($u['value']) ?>"><?= e($u['label']) ?></option>
              <?php endforeach; ?>
            </select>
          </p>
        </div>
      </div>

      <!-- 5. Bedrooms -->
      <div class="fieldset" role="group" aria-labelledby="fs5-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs5-label">
            5. Number of Bedrooms<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Configuration</span>
        </div>

        <div class="segments">
          <?php foreach (bedroom_options() as $bed): ?>
            <?= segment('bedrooms', $bed, $bed['value'] === '4') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 6. Ownership -->
      <div class="fieldset" role="group" aria-labelledby="fs6-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs6-label">
            6. Property Ownership<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Title Structure</span>
        </div>

        <div class="options options--2">
          <?php foreach (ownership_types() as $own): ?>
            <?= option_marked('ownership', $own, $own['value'] === 'individual') ?>
          <?php endforeach; ?>
        </div>

        <p class="note">
          <?= icon('info', 'icon--sm') ?>
          Sensitive title deeds, patta, and legal documents are gathered
          strictly during Step 02 under encrypted verification.
        </p>
      </div>

      <!-- Actions -->
      <div class="form-foot">
        <p class="form-foot__status">
          <strong>Step 1 of 3</strong>
          <span aria-hidden="true">&bull;</span>
          Next: Details &amp; Documents
        </p>

        <div class="form-foot__actions">
          <button class="link-quiet" type="submit" name="action" value="save">
            Save &amp; continue later
          </button>
          <button class="btn-submit" type="submit" name="action" value="continue">
            Continue
            <?= icon('arrow-right', 'icon--sm') ?>
          </button>
        </div>
      </div>

    </form>
  </div>
</main>

<?php include __DIR__ . '/../includes/intake-footer.php'; ?>
