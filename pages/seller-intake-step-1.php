<?php
/**
 * HS Properties — Private Seller Intake, Step 01: Contact Details.
 *
 * Collects fundamental contact information:
 *   - Full Name (Required)
 *   - Phone Number (Required)
 *   - Email Address (Required)
 *   - Address (Optional)
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/seller-intake-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$pageTitle   = 'Private Seller Intake — Step 01: Contact Details | HS Properties';
$intakeStep  = 1;
$intakeFlow  = 'Seller Intake';
$intakeSteps = intake_steps();

$draft = Session::get('seller_intake', []);
$step1Data = $draft['step1'] ?? [];
$errors = Session::flash('errors') ?? [];
$old = Session::flash('old') ?? [];
$notice = Session::flash('notice');
$v = array_merge($step1Data, $old);

include __DIR__ . '/../includes/intake-header.php';
?>

<main class="intake-main">
  <div class="container intake-grid">

    <!-- ================= Editorial column ================= -->
    <aside class="intake-aside">
      <p class="intake-aside__eyebrow">Private Property Mandate</p>

      <h1 class="intake-aside__title">
        Your contact
        <em>details.</em>
      </h1>

      <p class="intake-aside__lede">
        Start with the essentials. We&rsquo;ll use these details to connect with you
        directly before discussing your property, valuation, and discreet buyer positioning.
      </p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Advisory Protocol</h2>
        <p class="protocol__text">
          Your information is handled confidentially. Submissions are never
          shared publicly or broadcast across portals.
        </p>
      </div>

      <ul class="assurances">
        <li class="assurances__item">Direct oversight by a dedicated Senior Managing Partner.</li>
        <li class="assurances__item">Encrypted intake &amp; strict client confidentiality.</li>
        <li class="assurances__item">Access restricted solely to vetted advisory personnel.</li>
      </ul>

      <p class="phase">
        <span class="phase__number">01</span>
        <span class="phase__total">/03</span>
        <span class="phase__label">Phase: Contact Details</span>
      </p>
    </aside>

    <!-- ================= Form column ================= -->
    <form
      class="panel"
      id="intake-form"
      method="post"
      action="<?= e(url('api/seller-intake.php?step=1')) ?>">

      <?= Session::csrfField() ?>

      <?php if (!empty($notice)): ?>
        <div style="background: rgba(46, 125, 50, 0.08); border-left: 3px solid #2e7d32; padding: 12px 16px; margin-bottom: 24px; border-radius: 4px; color: #1b5e20; font-size: 0.875rem;">
          <?= e($notice) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div style="background: rgba(185, 28, 28, 0.08); border-left: 3px solid #b91c1c; padding: 12px 16px; margin-bottom: 24px; border-radius: 4px;">
          <strong style="color: #b91c1c; font-size: 0.9rem; display: block; margin-bottom: 6px;">Please review the highlighted details:</strong>
          <ul style="margin: 0; padding-left: 18px; color: #7f1d1d; font-size: 0.85rem;">
            <?php foreach ($errors as $err): ?>
              <li><?= e($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="panel__head">
        <div>
          <h2 class="panel__title">Contact Information</h2>
          <p class="panel__note">
            Provide your primary contact details to begin the private consultation process.
          </p>
        </div>
        <span class="panel__tag">Confidential Intake</span>
      </div>

      <div class="fieldset">
        <div class="fieldset__head">
          <span class="fieldset__legend">Primary Contact Details</span>
          <span class="fieldset__hint">Direct Principal</span>
        </div>

        <!-- 1. Full Name -->
        <div class="input-group">
          <label class="input-group__label" for="full_name">
            Full Name<span class="req" aria-hidden="true">*</span>
          </label>
          <input
            class="input"
            type="text"
            id="full_name"
            name="full_name"
            placeholder="e.g. Alexander Vance"
            autocomplete="name"
            value="<?= e($v['full_name'] ?? $v['contact_name'] ?? '') ?>"
            required>
        </div>

        <!-- 2. Phone Number & 3. Email Address -->
        <div class="grid-2 u-mt">
          <div class="input-group">
            <label class="input-group__label" for="phone">
              Phone Number<span class="req" aria-hidden="true">*</span>
            </label>
            <input
              class="input"
              type="tel"
              id="phone"
              name="phone"
              placeholder="+91 98765 43210"
              autocomplete="tel"
              value="<?= e($v['phone'] ?? $v['contact_phone'] ?? '') ?>"
              required>
          </div>

          <div class="input-group">
            <label class="input-group__label" for="email">
              Email Address<span class="req" aria-hidden="true">*</span>
            </label>
            <input
              class="input"
              type="email"
              id="email"
              name="email"
              placeholder="alexander.vance@example.com"
              autocomplete="email"
              value="<?= e($v['email'] ?? $v['contact_email'] ?? '') ?>"
              required>
          </div>
        </div>

        <!-- 4. Address -->
        <div class="input-group u-mt">
          <label class="input-group__label" for="address">
            Address <span class="muted" style="font-size: 0.85em; font-weight: normal; color: var(--c-sand-500);">(Optional)</span>
          </label>
          <input
            class="input"
            type="text"
            id="address"
            name="address"
            placeholder="Residential or correspondence address"
            autocomplete="street-address"
            value="<?= e($v['address'] ?? $v['contact_address'] ?? '') ?>">
        </div>

        <p class="note">
          <?= icon('lock', 'icon--sm') ?>
          Your contact details are encrypted and held in strict confidence. We never share your details or broadcast them across public channels.
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
