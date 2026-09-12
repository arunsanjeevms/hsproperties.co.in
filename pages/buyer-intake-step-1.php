<?php
/**
 * HS Properties — Private Buyer Intake, Step 01: Profile.
 *
 * Frontend stage: the form posts straight to Step 02, which simply renders.
 * When the backend lands, that action points at a controller that persists the
 * step and redirects — the markup does not need to change.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/buyer-intake-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$pageTitle   = 'Private Buyer Intake — Step 01: Profile | HS Properties';
$intakeStep  = 1;
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
        Tell us who we are
        <em>acting for.</em>
      </h1>

      <p class="intake-aside__lede">
        Start with the principal. These details let us qualify the mandate and
        decide which off-market properties may be shared with you &mdash; and
        how we should reach you about them.
      </p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Privacy Protocol</h2>
        <p class="protocol__text">
          Your identity is never disclosed to a seller without your written
          consent. Requirements are never circulated across broker networks.
        </p>
      </div>

      <ul class="assurances">
        <li class="assurances__item">Direct oversight by a dedicated Senior Managing Partner.</li>
        <li class="assurances__item">Encrypted intake &amp; strict pre-NDA buyer qualification.</li>
        <li class="assurances__item">Access to mandates never published on public portals.</li>
      </ul>

      <p class="phase">
        <span class="phase__number">01</span>
        <span class="phase__total">/03</span>
        <span class="phase__label">Phase: Principal Identification</span>
      </p>
    </aside>

    <!-- ================= Form column ================= -->
    <form
      class="panel"
      id="intake-form"
      method="post"
      action="<?= e(route('buyer.step2')) ?>">

      <div class="panel__head">
        <div>
          <h2 class="panel__title">Buyer Profile</h2>
          <p class="panel__note">
            Begin with the principal behind the acquisition and how we should
            correspond.
          </p>
        </div>
        <span class="panel__tag">Confidential Intake</span>
      </div>

      <!-- 1. Buyer profile -->
      <div class="fieldset" role="group" aria-labelledby="fs1-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs1-label">
            1. Buyer Profile<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Select single archetype</span>
        </div>

        <div class="options options--3">
          <?php foreach (buyer_profiles() as $profile): ?>
            <?= option_card('buyer_profile', $profile, $profile['value'] === 'individual') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 2. Acquisition intent -->
      <div class="fieldset" role="group" aria-labelledby="fs2-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs2-label">
            2. Acquisition Intent<span class="req" aria-hidden="true">*</span>
          </span>
        </div>

        <div class="options options--3">
          <?php foreach (buyer_intents() as $intent): ?>
            <?= segment('intent', $intent, $intent['value'] === 'primary') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 3. Principal details -->
      <div class="fieldset" role="group" aria-labelledby="fs3-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs3-label">
            3. Principal Details<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Confidential Registry</span>
        </div>

        <div class="grid-2">
          <p class="input-group">
            <label class="input-group__label" for="full-name">Full Name</label>
            <input
              class="input" type="text" id="full-name" name="full_name"
              placeholder="A. Raghavan" autocomplete="name" required>
          </p>
          <p class="input-group">
            <label class="input-group__label" for="entity">Entity / Family Office <span class="muted">(if applicable)</span></label>
            <input class="input" type="text" id="entity" name="entity" placeholder="Raghavan Family Office">
          </p>
        </div>

        <div class="grid-2 u-mt">
          <p class="input-group">
            <label class="input-group__label" for="phone">Private Contact Number</label>
            <span class="affix affix--lead">
              <span class="affix__lead" aria-hidden="true">+91</span>
              <input
                class="input" type="tel" id="phone" name="phone"
                placeholder="98450 00000" inputmode="tel"
                autocomplete="tel" required>
            </span>
          </p>
          <p class="input-group">
            <label class="input-group__label" for="email">Email Address</label>
            <input
              class="input" type="email" id="email" name="email"
              placeholder="principal@familyoffice.com"
              autocomplete="email" required>
          </p>
        </div>

        <p class="note">
          <?= icon('lock', 'icon--sm') ?>
          Contact details are used solely by the client desk. We never add you
          to marketing lists or share your number with sellers or agents.
        </p>
      </div>

      <!-- 4. Residency -->
      <div class="fieldset" role="group" aria-labelledby="fs4-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs4-label">
            4. Residency &amp; Base<span class="req" aria-hidden="true">*</span>
          </span>
        </div>

        <div class="grid-2">
          <p class="input-group">
            <label class="input-group__label" for="base-city">Current City of Residence</label>
            <input class="input" type="text" id="base-city" name="base_city" placeholder="Bengaluru" required>
          </p>
          <p class="input-group">
            <label class="input-group__label" for="country">Country</label>
            <input
              class="input" type="text" id="country" name="country"
              placeholder="India" autocomplete="country-name" required>
          </p>
        </div>
      </div>

      <!-- 5. Correspondence -->
      <div class="fieldset" role="group" aria-labelledby="fs5-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs5-label">
            5. Preferred Correspondence<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Discreet Channel</span>
        </div>

        <div class="segments" role="radiogroup" aria-labelledby="fs5-label">
          <?php foreach (contact_channels() as $channel): ?>
            <?= segment('contact_channel', $channel, $channel['value'] === 'phone') ?>
          <?php endforeach; ?>
        </div>

        <p class="group-label" id="window-label">Preferred Contact Window</p>
        <div class="segments" role="radiogroup" aria-labelledby="window-label">
          <?php foreach (contact_windows() as $window): ?>
            <?= segment('contact_window', $window, $window['value'] === 'evening') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 6. Representation -->
      <div class="fieldset" role="group" aria-labelledby="fs6-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs6-label">
            6. Representation<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Mandate Structure</span>
        </div>

        <div class="options options--2">
          <?php foreach (representation_types() as $rep): ?>
            <?= option_marked('representation', $rep, $rep['value'] === 'direct') ?>
          <?php endforeach; ?>
        </div>

        <p class="note">
          <?= icon('info', 'icon--sm') ?>
          Proof of funds and identity verification are requested strictly during
          Step 02 under encrypted handling, and only once a property is matched.
        </p>
      </div>

      <!-- Actions -->
      <div class="form-foot">
        <p class="form-foot__status">
          <strong>Step 1 of 3</strong>
          <span aria-hidden="true">&bull;</span>
          Next: Preferences &amp; Budget
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
