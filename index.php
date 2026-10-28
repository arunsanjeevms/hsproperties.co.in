<?php
/**
 * HS Properties — Home
 * "Architectural Editorial Edition"
 *
 * Section order follows the Stitch blueprint:
 *   1 Hero · 2 Trust pillars · 3 Portfolio · 4 Advisory mandate
 *   5 Buyer journey · 6 Seller journey · 7 Distinction · 8 Governance
 *   9 Closing + advisory bar
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/data.php';

$currentPage = 'home';
$pageTitle = 'HS Properties — Private Real Estate Advisory';

include __DIR__ . '/includes/header.php';
?>

<main class="site-main" id="main">

  <?php if ($successMsg = Session::flash('success')): ?>
    <div class="container" style="padding-top: var(--s-md); position: relative; z-index: 50;">
      <div style="background: rgba(34, 197, 94, 0.15); border: 1px solid rgba(34, 197, 94, 0.4); color: #86efac; padding: 14px 20px; border-radius: var(--r-md); margin-top: 10px; display: flex; align-items: center; gap: 12px; backdrop-filter: blur(8px);">
        <?= icon('verified', 'icon--sm') ?>
        <span><?= e($successMsg) ?></span>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($errorMsg = Session::flash('error')): ?>
    <div class="container" style="padding-top: var(--s-md); position: relative; z-index: 50;">
      <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #fca5a5; padding: 14px 20px; border-radius: var(--r-md); margin-top: 10px; display: flex; align-items: center; gap: 12px; backdrop-filter: blur(8px);">
        <?= icon('info', 'icon--sm') ?>
        <span><?= e($errorMsg) ?></span>
      </div>
    </div>
  <?php endif; ?>

  <!-- ====================================================================
       1. HERO — cinematic architectural plate
       ==================================================================== -->
  <section class="hero" aria-labelledby="hero-title">
    <img
      class="hero__plate ken-burns"
      src="<?= e(url('assets/images/hero-villa.jpg')) ?>?v=<?= @filemtime(__DIR__ . '/assets/images/hero-villa.jpg') ?: '2' ?>"
      alt="A modern Indian luxury villa with warm sandstone cantilevered volumes, floor-to-ceiling glass and a still reflecting pool, photographed at dusk beneath coastal palms."
      fetchpriority="high"
      decoding="async">

    <div class="hero__scrim hero__scrim--horizontal"></div>
    <div class="hero__scrim hero__scrim--vertical"></div>

    <div class="container hero__inner">
      <div class="hero__content">

        <p class="badge badge--glass hero-item" data-step="0">
          <span class="badge__dot"></span>
          Private Real Estate Advisory
        </p>

        <h1 class="t-display hero__title hero-item" id="hero-title" data-step="1">
          The Right Property.<br>
          <span class="t-italic t-champagne">The Right Connection.</span>
        </h1>

        <p class="t-body-lg hero__lede hero-item" data-step="2">
          A private, curated approach to acquiring and presenting exceptional
          residential and estate assets across India's premier enclaves.
        </p>

        <p class="t-body-md hero__support hero-item" data-step="3">
          HS Properties connects serious buyers and property owners through a
          discreet advisory-led process — from mandate discovery and valuation
          to sovereign verification and confidential closing.
        </p>

        <!-- Dual gateway: the page's two primary intents, given equal weight -->
        <div class="hero__gateways hero-item" data-step="4">

          <a class="gateway gateway--solid shimmer" href="#buyer">
            <span class="gateway__row">
              <span class="gateway__label">
                <?= icon('shield', 'gateway__icon') ?>
                Find Your Property
              </span>
              <?= icon('arrow-right', 'icon--arrow gateway__icon') ?>
            </span>
            <span class="gateway__note">
              Specify your parameters. We curate private opportunities.
            </span>
          </a>

          <a class="gateway gateway--glass shimmer" href="<?= e(route('seller.step1')) ?>">
            <span class="gateway__row">
              <span class="gateway__label">
                <?= icon('villa', 'gateway__icon') ?>
                Sell Your Property
              </span>
              <?= icon('arrow-right', 'icon--arrow gateway__icon') ?>
            </span>
            <span class="gateway__note">
              Submit privately for discreet appraisal and qualified buyer access.
            </span>
          </a>

        </div>
      </div>
    </div>
  </section>

  <!-- ====================================================================
       2. TRUST PILLARS
       ==================================================================== -->
  <section class="pillars" aria-label="Why clients work with us">
    <div class="container">
      <div class="pillars__grid">
        <?php foreach (trust_pillars() as $i => $pillar): ?>
          <div class="pillar reveal" data-delay="<?= (int) $i ?>">
            <span class="pillar__icon"><?= icon($pillar['icon']) ?></span>
            <div>
              <h2 class="pillar__title"><?= e($pillar['title']) ?></h2>
              <p class="pillar__text"><?= e($pillar['text']) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ====================================================================
       3. PORTFOLIO — completed mandates
       ==================================================================== -->
  <section class="section section--dark" id="portfolio" aria-labelledby="portfolio-title">
    <div class="container">

      <div class="portfolio__head reveal">
        <div class="section-head">
          <p class="t-eyebrow section-head__eyebrow">Representative Portfolio</p>
          <h2 class="t-headline-lg section-head__title" id="portfolio-title">
            Notable Properties Sold &amp; Closed
          </h2>
          <p class="t-body-md section-head__lede">
            A retrospective portfolio of premier residential estates, penthouses,
            and private retreats successfully represented, acquired, and closed
            through our private advisory network.
          </p>
        </div>

        <a class="link-action" href="#">
          View Portfolio Archives
          <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
        </a>
      </div>

      <div class="portfolio__grid">
        <?php foreach (portfolio_properties() as $i => $property): ?>
          <article class="property reveal" data-delay="<?= (int) $i ?>">

            <figure class="property__figure">
              <img
                class="property__image"
                src="<?= e(url($property['image'])) ?>"
                alt="<?= e($property['image_alt']) ?>"
                loading="lazy"
                decoding="async">

              <span class="badge badge--status property__status">
                <?= e($property['status']) ?>
              </span>

              <figcaption class="property__meta">
                <span><?= e($property['location']) ?></span>
                <span class="property__ref">Ref: <?= e($property['reference']) ?></span>
              </figcaption>
            </figure>

            <div class="property__body">
              <h3 class="property__title"><?= e($property['title']) ?></h3>
              <p class="property__mandate"><?= e($property['mandate']) ?></p>
              <p class="property__spec"><?= e($property['spec']) ?></p>
              <p class="property__summary"><?= e($property['summary']) ?></p>
            </div>

            <div class="property__footer">
              <span class="property__type"><?= e($property['type']) ?></span>
              <a class="link-action" href="#">
                <span class="visually-hidden"><?= e($property['title']) ?> — </span>
                Mandate Case Study
                <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
              </a>
            </div>

          </article>
        <?php endforeach; ?>
      </div>

      <div class="portfolio__footer reveal">
        <a class="btn btn--light btn--lg shimmer" href="#">
          Explore All Closed Transactions
          <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
        </a>
      </div>

    </div>
  </section>

  <!-- ====================================================================
       4. ADVISORY MANDATE — continuous four-stage band
       ==================================================================== -->
  <section class="section section--ivory" id="advisory" aria-labelledby="advisory-title">
    <div class="container">

      <div class="section-head reveal">
        <p class="t-eyebrow section-head__eyebrow">The Advisory Mandate</p>
        <h2 class="t-headline-lg section-head__title" id="advisory-title">
          Property decisions deserve more than a search bar.
        </h2>
        <p class="t-body-md section-head__lede">
          From defining your acquisition criteria to evaluating unlisted
          inventory, verifying encumbrance certificates, coordinating private
          viewings, and structuring final contracts — HS Properties provides a
          trusted partner at every juncture.
        </p>
      </div>

      <ol class="mandate__band reveal">
        <?php foreach (advisory_stages() as $stage): ?>
          <li class="stage">
            <div class="stage__head">
              <span class="stage__number" aria-hidden="true"><?= e($stage['number']) ?></span>
              <span class="stage__label"><?= e($stage['label']) ?></span>
            </div>
            <h3 class="stage__title"><?= e($stage['title']) ?></h3>
            <p class="stage__text"><?= e($stage['text']) ?></p>
            <p class="stage__footer">
              <?= e($stage['tag']) ?>
              <span class="stage__dot" aria-hidden="true"></span>
            </p>
          </li>
        <?php endforeach; ?>
      </ol>

    </div>
  </section>

  <!-- ====================================================================
       5. BUYER JOURNEY — asymmetric split with a vertical journey line
       ==================================================================== -->
  <section class="section buyer" id="buyer" aria-labelledby="buyer-title">
    <div class="container">
      <div class="buyer__grid">

        <div class="reveal">
          <div class="section-head">
            <p class="t-eyebrow section-head__eyebrow">For Discerning Buyers</p>
            <h2 class="t-headline-lg section-head__title" id="buyer-title">
              Looking for something specific?
            </h2>
            <p class="t-body-md section-head__lede">
              Tell us what matters to you. Our advisors translate your
              parameters into a targeted acquisition mandate, saving hundreds of
              hours of unfiltered viewings.
            </p>
          </div>

          <p class="buyer__note">
            <?= icon('verified', 'icon--sm') ?>
            Zero spam. No unsolicited broker circulars. Pure bespoke curation.
          </p>

          <div class="buyer__cta">
            <button type="button" class="btn btn--primary btn--lg shimmer btn-open-buyer-modal">
              Register Acquisition Mandate
              <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
            </button>
          </div>
        </div>

        <ol class="journey">
          <?php foreach (buyer_journey() as $i => $step): ?>
            <li class="journey__step reveal" data-delay="<?= (int) $i ?>">
              <span class="journey__marker" aria-hidden="true"></span>
              <div class="journey__head">
                <span class="journey__index"><?= e($step['index']) ?></span>
                <h3 class="journey__title"><?= e($step['title']) ?></h3>
              </div>
              <p class="journey__text"><?= e($step['text']) ?></p>
            </li>
          <?php endforeach; ?>
        </ol>

      </div>
    </div>
  </section>

  <!-- ====================================================================
       6. SELLER JOURNEY — dark band with a horizontal timeline
       ==================================================================== -->
  <section class="section section--dark seller" id="seller" aria-labelledby="seller-title">
    <img
      class="seller__plate"
      src="<?= e(url('assets/images/villa-terrace.jpg')) ?>"
      alt=""
      aria-hidden="true"
      loading="lazy"
      decoding="async">

    <div class="container seller__inner">

      <div class="section-head reveal">
        <p class="t-eyebrow section-head__eyebrow">For Property Owners</p>
        <h2 class="t-headline-lg section-head__title" id="seller-title">
          Have a property worth presenting differently?
        </h2>
        <p class="t-body-md section-head__lede">
          Submit your property privately. We evaluate its positioning,
          documentation, and buyer alignment before deciding how it enters our
          private network.
        </p>
      </div>

      <div class="confidentiality reveal">
        <span class="confidentiality__icon"><?= icon('lock') ?></span>
        <p class="confidentiality__text">
          <strong>Strict Confidentiality:</strong>
          Your property is not automatically published publicly. Every
          submission undergoes discreet partner evaluation and selective
          introduction to accredited buyers.
        </p>
      </div>

      <ol class="timeline">
        <?php foreach (seller_journey() as $i => $step): ?>
          <li class="timeline__step reveal" data-delay="<?= (int) $i ?>">
            <div class="timeline__head">
              <span class="timeline__marker" aria-hidden="true"><?= e($step['index']) ?></span>
              <span class="timeline__label"><?= e($step['label']) ?></span>
            </div>
            <h3 class="timeline__title"><?= e($step['title']) ?></h3>
            <p class="timeline__text"><?= e($step['text']) ?></p>
          </li>
        <?php endforeach; ?>
      </ol>

      <div class="seller__trigger reveal">
        <p class="seller__assurance">
          <?= icon('gavel', 'icon--sm') ?>
          Non-disclosure agreements executed before disclosing property addresses.
        </p>
        <a class="btn btn--light btn--lg shimmer" href="<?= e(route('seller.step1')) ?>">
          Sell Your Property
          <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
        </a>
      </div>

    </div>
  </section>

  <!-- ====================================================================
       7. DISTINCTION — editorial split with an overlapping float card
       ==================================================================== -->
  <section class="section section--stone" id="distinction" aria-labelledby="distinction-title">
    <div class="container">
      <div class="distinction__grid">

        <div class="distinction__visual reveal">
          <img
            class="distinction__image"
            src="<?= e(url('assets/images/villa-terrace.jpg')) ?>"
            alt="A warm stone poolside terrace of a private Indian villa at dusk, with minimalist landscaping and large glass panes lit from within."
            loading="lazy"
            decoding="async">

          <div class="float-card">
            <p class="float-card__label">
              <span class="float-card__dot" aria-hidden="true"></span>
              Discreet Network
            </p>
            <p class="float-card__text">
              Over 65% of our high-value residential advisory transactions are
              conducted strictly off-market.
            </p>
          </div>
        </div>

        <div class="reveal">
          <div class="section-head">
            <p class="t-eyebrow section-head__eyebrow">Distinction</p>
            <h2 class="t-headline-lg section-head__title" id="distinction-title">
              A better way to navigate high-value property decisions.
            </h2>
            <p class="t-body-md section-head__lede">
              Traditional real estate classifieds optimize for page views, ad
              revenue, and bulk broker circulars. HS Properties is founded on
              the antithesis: low volume, senior advisor stewardship, absolute
              confidentiality, and deep asset verification.
            </p>
          </div>

          <div class="distinction__features">
            <?php foreach (distinction_points() as $point): ?>
              <div class="card feature feature--inline card--lift">
                <div class="feature__head">
                  <?= icon($point['icon'], 'icon--sm') ?>
                  <h3 class="feature__title"><?= e($point['title']) ?></h3>
                </div>
                <p class="feature__text"><?= e($point['text']) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ====================================================================
       8. INSTITUTIONAL GOVERNANCE
       ==================================================================== -->
  <section class="section section--dark" id="governance" aria-labelledby="governance-title">
    <div class="container">

      <div class="section-head reveal">
        <p class="t-eyebrow section-head__eyebrow">Institutional Governance</p>
        <h2 class="t-headline-lg section-head__title" id="governance-title">
          Built around trust.
        </h2>
        <p class="t-body-md section-head__lede">
          Property acquisitions and liquidations involve monumental capital
          allocation, estate planning, and family legacy. HS Properties operates
          with the disciplined fiduciary standard of private banking.
        </p>
      </div>

      <div class="governance__grid">
        <?php foreach (governance_pillars() as $i => $pillar): ?>
          <div class="card card--dark card--lift feature reveal" data-delay="<?= (int) $i ?>">
            <span class="feature__icon"><?= icon($pillar['icon']) ?></span>
            <h3 class="feature__title"><?= e($pillar['title']) ?></h3>
            <p class="feature__text"><?= e($pillar['text']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ====================================================================
       9. CLOSING — editorial split, then the direct advisory bar
       ==================================================================== -->
  <section class="closing" id="contact" aria-labelledby="closing-title">
    <span class="closing__glow" aria-hidden="true"></span>

    <div class="container">
      <div class="closing__grid">

        <div class="closing__copy reveal">
          <div class="section-head">
            <p class="t-eyebrow section-head__eyebrow">Let's Take the Next Step</p>
            <h2 class="t-headline-lg section-head__title" id="closing-title">
              A conversation today, a
              <span class="t-italic t-champagne">better</span> tomorrow.
            </h2>
          </div>

          <p class="t-body-md closing__lede">
            Whether you're looking for your next home or considering selling a
            valued asset, we're here to listen, understand, and guide you with
            discretion and expertise.
          </p>

          <div class="closing__cards">

            <a class="choice btn-open-buyer-modal" href="#buyer">
              <?= icon('home', 'choice__icon icon--lg') ?>
              <span class="choice__eyebrow">For Buyers</span>
              <span class="choice__title">Find Your Property</span>
              <span class="choice__text">
                Share your requirements and let us curate the right opportunities.
              </span>
              <span class="choice__action">
                Register Mandate
                <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
              </span>
            </a>

            <a class="choice choice--solid" href="<?= e(route('seller.step1')) ?>">
              <?= icon('document', 'choice__icon icon--lg') ?>
              <span class="choice__eyebrow">For Sellers</span>
              <span class="choice__title">Sell Your Property</span>
              <span class="choice__text">
                Submit your property privately for evaluation and advisory.
              </span>
              <span class="choice__action">
                Get Started
                <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
              </span>
            </a>

          </div>

          <!-- Direct Advisory Contact Form -->
          <div class="closing__form reveal" style="margin-top: var(--s-xl); padding: var(--s-lg); border: 1px solid var(--c-champagne-a30); border-radius: var(--r-md); background: rgba(28, 17, 10, 0.7); backdrop-filter: blur(12px);">
            <div style="margin-bottom: var(--s-md);">
              <h3 style="font-family: var(--font-display); font-size: 1.25rem; color: var(--c-champagne-300); margin-top: 0; margin-bottom: 4px;">Direct Consultation Inquiry</h3>
              <p style="font-size: 0.82rem; color: var(--c-surface-highest); margin: 0;">Leave a confidential brief for our managing advisors.</p>
            </div>

            <div class="form-feedback" id="contact-feedback"></div>

            <form id="form-contact-desk" method="post" action="<?= e(url('api/contact.php')) ?>">
              <?= Session::csrfField() ?>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="cf-name">Name *</label>
                  <input type="text" id="cf-name" name="full_name" class="form-control" placeholder="Vikramaditya Rao" required>
                </div>
                <div class="form-group">
                  <label class="form-label" for="cf-email">Email *</label>
                  <input type="email" id="cf-email" name="email" class="form-control" placeholder="rao@advisory.com" required>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label" for="cf-phone">Telephone</label>
                  <input type="tel" id="cf-phone" name="phone" class="form-control" placeholder="+91 98450 12345">
                </div>
                <div class="form-group">
                  <label class="form-label" for="cf-subject">Subject</label>
                  <input type="text" id="cf-subject" name="subject" class="form-control" placeholder="Confidential Estate Consultation">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="cf-msg">Message / Advisory Requirement *</label>
                <textarea id="cf-msg" name="message" class="form-control" rows="3" placeholder="Share your specific acquisition or advisory criteria..." required></textarea>
              </div>
              <div style="display: flex; justify-content: flex-end; margin-top: var(--s-sm);">
                <button type="submit" class="btn btn--light btn--md shimmer" id="btn-submit-contact">
                  Transmit Consultation Request
                  <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="closing__frame">
          <img
            class="closing__frame-image"
            src="<?= e(url('assets/images/villa-terrace.jpg')) ?>"
            alt=""
            aria-hidden="true"
            loading="lazy"
            decoding="async">
          <span class="closing__frame-scrim" aria-hidden="true"></span>

          <blockquote class="pull-quote reveal">
            <p class="pull-quote__text">
              &ldquo;More than properties, we build possibilities.&rdquo;
            </p>
            <footer class="pull-quote__attrib">HS Properties</footer>
          </blockquote>
        </div>

      </div>
    </div>

    <!-- Direct advisory bar -->
    <div class="advisory-bar">
      <div class="container advisory-bar__inner">

        <div class="advisory-bar__contact">
          <span class="advisory-bar__icon"><?= icon('call') ?></span>
          <div>
            <p class="advisory-bar__title">Prefer to speak directly?</p>
            <p class="advisory-bar__note">
              Talk to a Senior Advisor for a confidential consultation.
            </p>
          </div>
        </div>

        <a class="btn btn--light btn--lg shimmer" href="<?= e(route('advisor.call')) ?>">
          Speak to an Advisor
          <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
        </a>

        <div class="advisory-bar__brand">
          <p class="advisory-bar__motto">Property. People. Possibilities.</p>
          <p class="advisory-bar__wordmark">
            HS Properties
            <span>Private Real Estate Advisory</span>
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- ====================================================================
       BUYER MANDATE MODAL (PROGRESSIVE ENHANCEMENT)
       ==================================================================== -->
  <div class="modal-overlay" id="modal-buyer-mandate" role="dialog" aria-modal="true" aria-labelledby="modal-buyer-title">
    <div class="modal-panel">
      <button type="button" class="modal-close" id="btn-close-buyer-modal" aria-label="Close modal">&times;</button>
      <p class="t-eyebrow" style="color: var(--c-secondary); margin-bottom: 4px;">Private Acquisition Desk</p>
      <h2 id="modal-buyer-title" style="font-family: var(--font-display); font-size: 1.6rem; color: #faf6f1; margin-top: 0; margin-bottom: 12px;">Discreet Buyer Mandate</h2>
      <p style="font-size: 0.85rem; color: var(--c-surface-highest); margin-bottom: var(--s-lg); line-height: 1.5;">
        Register your acquisition parameters. Our senior partners search off-market inventory and curate matching estates under strict non-disclosure.
      </p>

      <div class="form-feedback" id="buyer-feedback"></div>

      <form id="form-buyer-mandate" method="post" action="<?= e(url('api/buyer-request.php')) ?>">
        <?= Session::csrfField() ?>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="bm-name">Full Name *</label>
            <input type="text" id="bm-name" name="full_name" class="form-control" placeholder="Rohan Singhania" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="bm-email">Direct Email *</label>
            <input type="email" id="bm-email" name="email" class="form-control" placeholder="singhania@domain.com" required>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="bm-phone">Phone / WhatsApp</label>
            <input type="tel" id="bm-phone" name="phone" class="form-control" placeholder="+91 98765 43210">
          </div>
          <div class="form-group">
            <label class="form-label" for="bm-type">Preferred Archetype</label>
            <select id="bm-type" name="property_type" class="form-control">
              <option value="Villa">Villa / Gated Luxury Enclave</option>
              <option value="Penthouse">Sky Penthouse / High-rise</option>
              <option value="Duplex">Urban Duplex</option>
              <option value="Estate">Estate / Country Acreage</option>
              <option value="Commercial">Commercial Landmark Parcel</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="bm-city">Target City / Region</label>
            <input type="text" id="bm-city" name="city" class="form-control" placeholder="Bengaluru / Mumbai / Goa">
          </div>
          <div class="form-group">
            <label class="form-label" for="bm-locality">Preferred Micro-market</label>
            <input type="text" id="bm-locality" name="locality" class="form-control" placeholder="Indiranagar, Whitefield, North Goa">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label" for="bm-budget">Indicative Budget Bracket</label>
            <select id="bm-budget" name="budget_max" class="form-control">
              <option value="₹ 5 Cr – ₹ 10 Cr">₹ 5 Cr – ₹ 10 Cr</option>
              <option value="₹ 10 Cr – ₹ 25 Cr" selected>₹ 10 Cr – ₹ 25 Cr</option>
              <option value="₹ 25 Cr – ₹ 50 Cr">₹ 25 Cr – ₹ 50 Cr</option>
              <option value="₹ 50 Cr+">₹ 50 Cr+ (Landmark Allocation)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label" for="bm-bedrooms">Configuration</label>
            <select id="bm-bedrooms" name="bedrooms" class="form-control">
              <option value="3 BHK">3 BHK</option>
              <option value="4 BHK" selected>4 BHK</option>
              <option value="5+ BHK">5+ BHK / Sprawling Grounds</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="bm-req">Bespoke Requirements / Architectural Preferences</label>
          <textarea id="bm-req" name="special_requirements" class="form-control" rows="2" placeholder="Private lap pool, cantilevered living spaces, high-security enclave..."></textarea>
        </div>

        <div style="margin-top: var(--s-md); display: flex; align-items: center; justify-content: space-between; gap: var(--s-md); flex-wrap: wrap;">
          <span style="font-size: 0.75rem; color: rgba(254, 207, 147, 0.7); display: flex; align-items: center; gap: 6px;">
            <?= icon('lock', 'icon--sm') ?>
            Zero broker syndication. Encrypted fiduciary handling.
          </span>
          <button type="submit" class="btn btn--light btn--lg shimmer" id="btn-submit-bm">
            Submit Private Mandate
            <?= icon('arrow-right', 'icon--arrow icon--sm') ?>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      // Modal controls
      var modal = document.getElementById('modal-buyer-mandate');
      var closeBtn = document.getElementById('btn-close-buyer-modal');
      var openBtns = document.querySelectorAll('.btn-open-buyer-modal');

      function openModal(e) {
        if (e) e.preventDefault();
        if (modal) modal.classList.add('is-open');
      }

      function closeModal() {
        if (modal) modal.classList.remove('is-open');
      }

      openBtns.forEach(function (btn) {
        btn.addEventListener('click', openModal);
      });

      if (closeBtn) closeBtn.addEventListener('click', closeModal);
      if (modal) {
        modal.addEventListener('click', function (e) {
          if (e.target === modal) closeModal();
        });
      }

      // AJAX form submission for Buyer Mandate
      var buyerForm = document.getElementById('form-buyer-mandate');
      var buyerFeedback = document.getElementById('buyer-feedback');
      var buyerBtn = document.getElementById('btn-submit-bm');

      if (buyerForm && buyerFeedback) {
        buyerForm.addEventListener('submit', function (e) {
          e.preventDefault();
          var formData = new FormData(buyerForm);
          var originalBtnText = buyerBtn ? buyerBtn.innerHTML : '';
          if (buyerBtn) {
            buyerBtn.textContent = 'Transmitting...';
            buyerBtn.disabled = true;
          }

          fetch(buyerForm.action, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data.success) {
              buyerFeedback.className = 'form-feedback form-feedback--success';
              buyerFeedback.innerHTML = '<strong>Mandate Registered.</strong> ' + data.message + ' (Ref: <strong>' + (data.reference || '') + '</strong>)';
              buyerForm.reset();
              setTimeout(function () {
                closeModal();
                buyerFeedback.style.display = 'none';
              }, 4000);
            } else {
              buyerFeedback.className = 'form-feedback form-feedback--error';
              var errs = data.errors ? Object.values(data.errors).join('<br>') : 'Submission error. Please check your entries.';
              buyerFeedback.innerHTML = errs;
            }
          })
          .catch(function () {
            // Fallback: standard submit
            buyerForm.submit();
          })
          .finally(function () {
            if (buyerBtn) {
              buyerBtn.innerHTML = originalBtnText;
              buyerBtn.disabled = false;
            }
          });
        });
      }

      // AJAX form submission for Contact Desk
      var contactForm = document.getElementById('form-contact-desk');
      var contactFeedback = document.getElementById('contact-feedback');
      var contactBtn = document.getElementById('btn-submit-contact');

      if (contactForm && contactFeedback) {
        contactForm.addEventListener('submit', function (e) {
          e.preventDefault();
          var formData = new FormData(contactForm);
          var origText = contactBtn ? contactBtn.innerHTML : '';
          if (contactBtn) {
            contactBtn.textContent = 'Transmitting...';
            contactBtn.disabled = true;
          }

          fetch(contactForm.action, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data.success) {
              contactFeedback.className = 'form-feedback form-feedback--success';
              contactFeedback.innerHTML = '<strong>Message Received.</strong> ' + data.message;
              contactForm.reset();
            } else {
              contactFeedback.className = 'form-feedback form-feedback--error';
              var errs = data.errors ? Object.values(data.errors).join('<br>') : 'Failed to send inquiry. Please check your entries.';
              contactFeedback.innerHTML = errs;
            }
          })
          .catch(function () {
            contactForm.submit();
          })
          .finally(function () {
            if (contactBtn) {
              contactBtn.innerHTML = origText;
              contactBtn.disabled = false;
            }
          });
        });
      }
    })();
  </script>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
