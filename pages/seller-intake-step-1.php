<?php
/**
 * HS Properties — Private Seller Intake (Web3Forms).
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/seller-intake-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$pageTitle   = 'Sell Your Property — Private Mandate | HS Properties';
$intakeStep  = 1;
$intakeFlow  = 'Seller Mandate';
$intakeSteps = [
    ['label' => 'Property Inquiry', 'href' => '#'],
];

include __DIR__ . '/../includes/intake-header.php';
?>

<main class="intake-main">
  <div class="container intake-grid">

    <!-- ================= Editorial column ================= -->
    <aside class="intake-aside">
      <p class="intake-aside__eyebrow">Private Property Mandate</p>

      <h1 class="intake-aside__title">
        Sell your
        <em>property.</em>
      </h1>

      <p class="intake-aside__lede">
        Submit your property details for discreet valuation, documentation auditing,
        and positioning with qualified accredited buyers under strict non-disclosure.
      </p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Advisory Protocol</h2>
        <p class="protocol__text">
          Your information is held in strict confidence. Submissions are never
          listed publicly, syndicated across search portals, or disclosed without prior bilateral agreement.
        </p>
      </div>

      <ul class="assurances">
        <li class="assurances__item">Direct oversight by a dedicated Senior Managing Partner.</li>
        <li class="assurances__item">Strict pre-NDA screening and encrypted transmission.</li>
        <li class="assurances__item">Curated access restricted solely to pre-qualified buyers.</li>
      </ul>
    </aside>

    <!-- ================= Form column ================= -->
    <div class="panel">
      <div class="panel__head">
        <div>
          <h2 class="panel__title">Property Placement Details</h2>
          <p class="panel__note">
            Please share the primary details below so our advisory desk can connect with you.
          </p>
        </div>
        <span class="panel__tag">Confidential Intake</span>
      </div>

      <div class="form-feedback" id="seller-feedback" style="display: none; padding: 12px 16px; margin-bottom: 20px; border-radius: 6px; font-size: 0.9rem;"></div>

      <form id="seller-intake-form" method="post" action="https://api.web3forms.com/submit">
        <input type="hidden" name="access_key" value="<?= e(web3forms_key()) ?>">
        <input type="hidden" name="subject" value="New Seller Inquiry — HS Properties">
        <input type="hidden" name="from_name" value="HS Properties Website">
        <input type="hidden" name="inquiry_type" value="Seller Mandate Inquiry">
        <input type="checkbox" name="botcheck" class="hidden" style="display: none;">

        <div class="fieldset">
          <div class="fieldset__head">
            <span class="fieldset__legend">Essential Information</span>
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
              name="name"
              placeholder="e.g. Alexander Vance"
              autocomplete="name"
              required>
          </div>

          <!-- 2. Phone Number -->
          <div class="input-group u-mt">
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
              required>
          </div>

          <!-- 3. Property Type (Dropdown) -->
          <div class="input-group u-mt">
            <label class="input-group__label" for="property_type">
              Property Type<span class="req" aria-hidden="true">*</span>
            </label>
            <select class="input" id="property_type" name="property_type" required>
              <option value="" disabled selected>Select property archetype *</option>
              <option value="Villa">Villa / Gated Luxury Enclave</option>
              <option value="Apartment / Penthouse">Apartment / Sky Penthouse</option>
              <option value="Independent House">Independent House / Standalone Bungalow</option>
              <option value="Plot / Land Parcel">Plot / Land Development Parcel</option>
              <option value="Commercial">Commercial / Prime Office / Retail</option>
              <option value="Estate / Farmhouse">Estate / Farmhouse / Country Acreage</option>
              <option value="Other">Other (Please specify)</option>
            </select>
          </div>

          <!-- 4. Dynamic "Other" text input -->
          <div class="input-group u-mt" id="other-type-wrap" style="display: none;">
            <label class="input-group__label" for="other_property_type">
              Specify Property Type<span class="req" aria-hidden="true">*</span>
            </label>
            <input
              class="input"
              type="text"
              id="other_property_type"
              name="other_property_type"
              placeholder="e.g. Heritage Haveli, Coffee Plantation, Island Parcel">
          </div>

          <p class="note">
            <?= icon('lock', 'icon--sm') ?>
            Submissions are transmitted securely via encrypted channel. No spam, no portal syndication.
          </p>
        </div>

        <div class="form-foot">
          <p class="form-foot__status">
            <strong>Direct Liaison</strong>
            <span aria-hidden="true">&bull;</span>
            Response within 1 business day
          </p>

          <div class="form-foot__actions">
            <a class="link-quiet" href="<?= e(url('index.html')) ?>">
              <?= icon('arrow-left', 'icon--sm') ?>
              Back to Home
            </a>
            <button class="btn-submit" type="submit" id="btn-seller-submit">
              Submit Property Details
              <?= icon('arrow-right', 'icon--sm') ?>
            </button>
          </div>
        </div>

      </form>
    </div>

  </div>
</main>

<script>
  (function () {
    var typeSelect = document.getElementById('property_type');
    var otherWrap  = document.getElementById('other-type-wrap');
    var otherInput = document.getElementById('other_property_type');

    if (typeSelect && otherWrap && otherInput) {
      typeSelect.addEventListener('change', function () {
        if (this.value === 'Other') {
          otherWrap.style.display = 'block';
          otherInput.required = true;
          otherInput.focus();
        } else {
          otherWrap.style.display = 'none';
          otherInput.required = false;
          otherInput.value = '';
        }
      });
    }

    var form = document.getElementById('seller-intake-form');
    var feedback = document.getElementById('seller-feedback');
    var btn = document.getElementById('btn-seller-submit');

    if (form && feedback) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        var formData = new FormData(form);
        if (formData.get('property_type') === 'Other' && formData.get('other_property_type')) {
          formData.set('property_type', 'Other (' + formData.get('other_property_type').trim() + ')');
        }

        var origText = btn ? btn.innerHTML : '';
        if (btn) {
          btn.textContent = 'Transmitting...';
          btn.disabled = true;
        }
        feedback.style.display = 'none';

        fetch('https://api.web3forms.com/submit', {
          method: 'POST',
          body: formData,
          headers: { 'Accept': 'application/json' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data.success) {
            feedback.style.display = 'block';
            feedback.style.background = 'rgba(46, 125, 50, 0.12)';
            feedback.style.borderLeft = '3px solid #2e7d32';
            feedback.style.color = '#1b5e20';
            feedback.innerHTML = '<strong>Property Details Received.</strong> Thank you! A senior advisory partner will connect with you shortly.';
            form.reset();
            if (otherWrap) otherWrap.style.display = 'none';
          } else {
            feedback.style.display = 'block';
            feedback.style.background = 'rgba(185, 28, 28, 0.08)';
            feedback.style.borderLeft = '3px solid #b91c1c';
            feedback.style.color = '#7f1d1d';
            feedback.innerHTML = '<strong>Notice:</strong> ' + (data.message || 'Please check your inputs and try again.');
          }
        })
        .catch(function () {
          form.submit();
        })
        .finally(function () {
          if (btn) {
            btn.innerHTML = origText;
            btn.disabled = false;
          }
        });
      });
    }
  })();
</script>

<?php include __DIR__ . '/../includes/intake-footer.php'; ?>
