<?php
/**
 * HS Properties — Private Seller Intake, Step 02: Details & Documents.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/seller-intake-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$pageTitle   = 'Private Seller Intake — Step 02: Details & Documents | HS Properties';
$intakeStep  = 2;
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
        Help us understand the
        <em>property.</em>
      </h1>

      <p class="intake-aside__lede">
        These details help us understand the property&rsquo;s condition,
        positioning, documentation, and potential buyer fit.
      </p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Privacy Protocol</h2>
        <p class="protocol__text">
          Your information is handled confidentially. Documentation is reviewed
          only as required for evaluation.
        </p>
      </div>

      <ul class="assurances">
        <li class="assurances__item">Property condition and documentation review.</li>
        <li class="assurances__item">Preliminary positioning and valuation context.</li>
        <li class="assurances__item">Preparation for curated buyer consideration.</li>
      </ul>

      <p class="phase">
        <span class="phase__number">02</span>
        <span class="phase__total">/03</span>
        <span class="phase__label">Phase: Physical Audit &amp; Documentation</span>
      </p>
    </aside>

    <!-- ================= Form column ================= -->
    <form
      class="panel"
      id="intake-form"
      method="post"
      enctype="multipart/form-data"
      action="<?= e(route('seller.step3')) ?>">

      <div class="panel__head">
        <div>
          <h2 class="panel__title">Property Details</h2>
          <p class="panel__note">
            Continue with the information required for preliminary property evaluation.
          </p>
        </div>
        <span class="panel__tag">Step 02 of 03</span>
      </div>

      <!-- 1. Location & condition -->
      <div class="fieldset" role="group" aria-labelledby="fs1-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs1-label">
            1. Physical Location &amp; Condition<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Confidential Registry</span>
        </div>

        <p class="input-group">
          <label class="input-group__label" for="address">Property Address or Estate Name</label>
          <input
            class="input" type="text" id="address" name="address"
            placeholder="42 Magnolia Sanctuary, Off Varthur Main Rd" required>
        </p>

        <div class="grid-3 u-mt">
          <p class="input-group">
            <label class="input-group__label" for="locality2">Locality / Micro-market</label>
            <input class="input" type="text" id="locality2" name="locality" placeholder="Whitefield / Palm Meadows">
          </p>
          <p class="input-group">
            <label class="input-group__label" for="city2">City</label>
            <input class="input" type="text" id="city2" name="city" placeholder="Bengaluru">
          </p>
          <p class="input-group">
            <label class="input-group__label" for="pincode2">PIN Code</label>
            <input
              class="input" type="text" id="pincode2" name="pincode"
              placeholder="560066" inputmode="numeric" pattern="[0-9]{6}"
              autocomplete="postal-code">
          </p>
        </div>

        <p class="group-label" id="condition-label">Physical Condition Appraisal</p>
        <div class="segments" role="radiogroup" aria-labelledby="condition-label">
          <?php foreach (condition_options() as $cond): ?>
            <?= segment('condition', $cond, $cond['value'] === 'pristine') ?>
          <?php endforeach; ?>
        </div>

        <div class="grid-1-2 u-mt">
          <p class="input-group">
            <label class="input-group__label" for="year">Year of Construction</label>
            <input
              class="input" type="text" id="year" name="year_built"
              placeholder="2021" inputmode="numeric" pattern="[0-9]{4}">
          </p>
          <div class="input-group">
            <span class="input-group__label" id="furnishing-label">Furnishing Status</span>
            <div class="segments" role="radiogroup" aria-labelledby="furnishing-label">
              <?php foreach (furnishing_options() as $f): ?>
                <?= segment('furnishing', $f, $f['value'] === 'full') ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- 2. Expectations & timeline -->
      <div class="fieldset" role="group" aria-labelledby="fs2-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs2-label">
            2. Selling Expectations &amp; Timeline<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Indicative Guidance</span>
        </div>

        <p class="input-group">
          <label class="input-group__label" for="price">
            Indicative Target Price (Net Realization Benchmark)
          </label>
          <span class="affix affix--lead affix--trail">
            <span class="affix__lead" aria-hidden="true">&#8377;</span>
            <input
              class="input" type="text" id="price" name="target_price"
              placeholder="18,50,00,000" inputmode="numeric">
            <span class="affix__trail" aria-hidden="true">INR</span>
          </span>
        </p>
        <p class="note note--plain">
          An indicative expectation is sufficient at this stage. Senior valuation
          partners perform independent micro-market auditing.
        </p>

        <p class="group-label" id="timeline-label">Target Liquidity Horizon</p>
        <div class="segments" role="radiogroup" aria-labelledby="timeline-label">
          <?php foreach (timeline_options() as $t): ?>
            <?= segment('timeline', $t, $t['value'] === '3-6m') ?>
          <?php endforeach; ?>
        </div>

        <p class="group-label" id="mandate-label">Preferred Mandate Structure</p>
        <div class="options options--3" role="radiogroup" aria-labelledby="mandate-label">
          <?php foreach (mandate_structures() as $m): ?>
            <?= option_card('mandate', $m, $m['value'] === 'off-market') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 3. Documentation -->
      <div class="fieldset" role="group" aria-labelledby="fs3-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs3-label">
            3. Documentation Available<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Encrypted Vault</span>
        </div>

        <div class="chips">
          <?php foreach (document_categories() as $doc): ?>
            <?= doc_chip('documents', $doc, $doc['staged']) ?>
          <?php endforeach; ?>
        </div>

        <label class="dropzone" id="dropzone">
          <input
            class="dropzone__input"
            type="file"
            id="document-files"
            name="document_files[]"
            multiple
            accept=".pdf,.dwg,.dxf,.jpg,.jpeg,.tiff">
          <span class="dropzone__lead">
            <?= icon('upload', 'icon--sm') ?>
            Drag &amp; drop legal deeds, surveys, or blueprints or
            <span class="dropzone__browse">browse files</span>
          </span>
          <span class="dropzone__formats">
            Supported formats: PDF, DWG, DXF, JPG, TIFF (Encrypted storage)
          </span>
        </label>

        <ul class="files" id="file-list">
          <?php foreach (array_slice(staged_documents(), 0, 2) as $doc): ?>
            <?= document_row($doc) ?>
          <?php endforeach; ?>
        </ul>

        <p class="note">
          <?= icon('lock', 'icon--sm') ?>
          Documents can also be provided during the review. Files are
          strictly utilized for legal encumbrance scrutiny and valuation
          verification.
        </p>
      </div>

      <!-- Actions -->
      <div class="form-foot">
        <p class="form-foot__status">
          <strong>Step 2 of 3</strong>
          <span aria-hidden="true">&bull;</span>
          Next: Review &amp; Submit
        </p>

        <div class="form-foot__actions">
          <a class="link-quiet" href="<?= e(route('seller.step1')) ?>">
            <?= icon('arrow-left', 'icon--sm') ?>
            Back to Step 01
          </a>
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
