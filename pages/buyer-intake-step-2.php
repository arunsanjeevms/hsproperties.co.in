<?php
/**
 * HS Properties — Private Buyer Intake, Step 02: Preferences & Budget.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/buyer-intake-data.php';
require_once __DIR__ . '/../includes/form-helpers.php';

$pageTitle   = 'Private Buyer Intake — Step 02: Preferences & Budget | HS Properties';
$intakeStep  = 2;
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
        Define what you are
        <em>looking for.</em>
      </h1>

      <p class="intake-aside__lede">
        These preferences shape the search. The tighter the brief, the more
        precisely we can match it against mandates that never reach the market.
      </p>

      <div class="protocol">
        <h2 class="protocol__title">Strict Privacy Protocol</h2>
        <p class="protocol__text">
          Budget guidance is indicative and held confidentially. It is never
          disclosed to a seller during negotiation without your instruction.
        </p>
      </div>

      <ul class="assurances">
        <li class="assurances__item">Micro-market mapping against the stated brief.</li>
        <li class="assurances__item">Budget positioned privately, never broadcast.</li>
        <li class="assurances__item">Matched only to pre-screened off-market mandates.</li>
      </ul>

      <p class="phase">
        <span class="phase__number">02</span>
        <span class="phase__total">/03</span>
        <span class="phase__label">Phase: Requirement Mapping</span>
      </p>
    </aside>

    <!-- ================= Form column ================= -->
    <form
      class="panel"
      id="intake-form"
      method="post"
      action="<?= e(route('buyer.step3')) ?>">

      <div class="panel__head">
        <div>
          <h2 class="panel__title">Preferences &amp; Budget</h2>
          <p class="panel__note">
            Continue with the requirement brief used for private matching.
          </p>
        </div>
        <span class="panel__tag">Step 02 of 03</span>
      </div>

      <!-- 1. Property type -->
      <div class="fieldset" role="group" aria-labelledby="fs1-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs1-label">
            1. Property Type Sought<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Select all that apply</span>
        </div>

        <div class="options options--3">
          <?php foreach (buyer_property_types() as $type): ?>
            <?= option_card('property_type', $type, $type['value'] === 'villa', 'checkbox') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 2. Target markets -->
      <div class="fieldset" role="group" aria-labelledby="fs2-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs2-label">
            2. Target Markets<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Confidential Registry</span>
        </div>

        <div class="grid-2">
          <p class="input-group">
            <label class="input-group__label" for="market-city">Primary City</label>
            <input class="input" type="text" id="market-city" name="market_city" placeholder="Bengaluru" required>
          </p>
          <p class="input-group">
            <label class="input-group__label" for="market-localities">Preferred Localities / Micro-markets</label>
            <input
              class="input" type="text" id="market-localities" name="market_localities"
              placeholder="Whitefield, Sadashivanagar, Dollars Colony" required>
          </p>
        </div>

        <p class="input-group u-mt">
          <label class="input-group__label" for="secondary-markets">
            Secondary Cities <span class="muted">(optional)</span>
          </label>
          <input
            class="input" type="text" id="secondary-markets" name="secondary_markets"
            placeholder="Pune — Koregaon Park; Goa — Assagao">
        </p>

        <p class="note">
          <?= icon('lock', 'icon--sm') ?>
          Stated localities are used for matching only. Your brief is never
          circulated to agents or listed as a buyer requirement.
        </p>
      </div>

      <!-- 3. Configuration -->
      <div class="fieldset" role="group" aria-labelledby="fs3-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs3-label">
            3. Configuration &amp; Size<span class="req" aria-hidden="true">*</span>
          </span>
        </div>

        <p class="group-label" id="bedrooms-label">Bedrooms Required</p>
        <div class="segments" role="radiogroup" aria-labelledby="bedrooms-label">
          <?php foreach (buyer_bedroom_options() as $bed): ?>
            <?= segment('bedrooms', $bed, $bed['value'] === '4') ?>
          <?php endforeach; ?>
        </div>

        <div class="grid-2 u-mt">
          <p class="input-group">
            <label class="input-group__label" for="min-extent">Minimum Built-up Area</label>
            <span class="affix affix--trail">
              <input
                class="input" type="text" id="min-extent" name="min_extent"
                placeholder="6000" inputmode="numeric">
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

      <!-- 4. Budget -->
      <div class="fieldset" role="group" aria-labelledby="fs4-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs4-label">
            4. Budget &amp; Funding<span class="req" aria-hidden="true">*</span>
          </span>
          <span class="fieldset__hint">Indicative Guidance</span>
        </div>

        <p class="group-label" id="band-label">Indicative Budget Band</p>
        <div class="segments" role="radiogroup" aria-labelledby="band-label">
          <?php foreach (budget_bands() as $band): ?>
            <?= segment('budget_band', $band, $band['value'] === '20-35cr') ?>
          <?php endforeach; ?>
        </div>

        <div class="grid-2 u-mt">
          <p class="input-group">
            <label class="input-group__label" for="budget-min">Lower Bound</label>
            <span class="affix affix--lead affix--trail">
              <span class="affix__lead" aria-hidden="true">&#8377;</span>
              <input
                class="input" type="text" id="budget-min" name="budget_min"
                placeholder="20,00,00,000" inputmode="numeric">
              <span class="affix__trail" aria-hidden="true">INR</span>
            </span>
          </p>
          <p class="input-group">
            <label class="input-group__label" for="budget-max">Upper Bound (Ceiling)</label>
            <span class="affix affix--lead affix--trail">
              <span class="affix__lead" aria-hidden="true">&#8377;</span>
              <input
                class="input" type="text" id="budget-max" name="budget_max"
                placeholder="35,00,00,000" inputmode="numeric">
              <span class="affix__trail" aria-hidden="true">INR</span>
            </span>
          </p>
        </div>

        <p class="note note--plain">
          An indicative range is sufficient at this stage. Senior partners audit
          micro-market pricing independently before any offer is positioned.
        </p>

        <p class="group-label" id="funding-label">Funding Route</p>
        <div class="options options--2" role="radiogroup" aria-labelledby="funding-label">
          <?php foreach (funding_routes() as $route): ?>
            <?= option_marked('funding', $route, $route['value'] === 'self') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 5. Timeline -->
      <div class="fieldset" role="group" aria-labelledby="fs5-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs5-label">
            5. Possession Horizon<span class="req" aria-hidden="true">*</span>
          </span>
        </div>

        <div class="segments" role="radiogroup" aria-labelledby="fs5-label">
          <?php foreach (possession_windows() as $window): ?>
            <?= segment('possession', $window, $window['value'] === '3-6m') ?>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- 6. Priorities -->
      <div class="fieldset" role="group" aria-labelledby="fs6-label">
        <div class="fieldset__head">
          <span class="fieldset__legend" id="fs6-label">
            6. Non-Negotiable Priorities
          </span>
          <span class="fieldset__hint">Multi-select</span>
        </div>

        <div class="chips">
          <?php foreach (buyer_amenities() as $amenity): ?>
            <?= doc_chip('amenities', $amenity, $amenity['staged']) ?>
          <?php endforeach; ?>
        </div>

        <p class="input-group u-mt">
          <label class="input-group__label" for="notes">
            Additional Brief <span class="muted">(optional)</span>
          </label>
          <textarea
            class="input" id="notes" name="notes" rows="4"
            placeholder="Corner plot preferred; east-facing; no ground-floor units; discretion essential as the purchase is not to be known locally."></textarea>
        </p>

        <p class="note">
          <?= icon('info', 'icon--sm') ?>
          Anything you would rather discuss than write down can be raised with
          the team at the qualification call.
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
          <a class="link-quiet" href="<?= e(route('buyer.step1')) ?>">
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
