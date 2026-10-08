<?php
/**
 * HS Properties — Properties | One property's file.
 *
 * Opened from the list's "View details" button. Same two-column console shape
 * as the request detail view: the file itself on the left, the things you act
 * on — status, next step, history — in the right rail.
 *
 * The page is written for staff who are not confident with software, so each
 * panel opens with a sentence saying what it is, the status is spelled out in
 * words underneath the coloured pill, and every match says in a sentence why
 * it is a match rather than only showing a percentage.
 *
 * An unknown code renders a not-found state rather than erroring; no code at
 * all opens the newest property, as the list's top row.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';
require_once __DIR__ . '/../../includes/form-helpers.php';

$code     = isset($_GET['code']) ? (string) $_GET['code'] : null;

// A status change posted from the rail comes back to this same file. Runs
// before any output, and before the property is read, so the page below always
// shows the status that was just saved.
$notice = $code === null ? null : admin_handle_status_post(admin_property_url($code));

$property = $code === null ? admin_default_property() : admin_property($code);

$pageTitle   = 'HS Properties — Properties';
$adminPage   = 'properties';
$adminCrumbs = [
    ['label' => 'Admin',      'href' => route('admin.dashboard')],
    ['label' => 'Properties', 'href' => route('admin.properties')],
    ['label' => $property ? $property['code'] : 'Unknown'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<?php if (!$property): ?>

  <section class="card">
    <div class="card__body">
      <h1 class="card__title">We could not find that property.</h1>
      <p class="card__note">
        <strong><?= e((string) $code) ?></strong> is not on our books. It may have
        been removed, or the code may have been typed wrongly.
      </p>
      <p style="margin-top: var(--s-md)">
        <a class="console-btn console-btn--gold" href="<?= e(route('admin.properties')) ?>">
          <?= icon('arrow-left', 'icon--sm') ?>
          Back to all properties
        </a>
      </p>
    </div>
  </section>

<?php else: ?>

  <?php
    $status    = admin_property_status($property['status']);
    $matches   = admin_property_matches($property['code']);
    $documents = admin_property_documents($property);
  ?>

  <!-- ================= Back bar ================= -->
  <section class="detail-bar">
    <a class="console-btn console-btn--quiet" href="<?= e(route('admin.properties')) ?>">
      <?= icon('arrow-left', 'icon--sm') ?>
      Back to all properties
    </a>
  </section>

  <?php if ($notice !== null): ?>
    <p class="notice notice--<?= $notice['ok'] ? 'ok' : 'bad' ?>" role="status">
      <?= icon($notice['ok'] ? 'verified' : 'info', 'icon--sm') ?>
      <?= e($notice['text']) ?>
    </p>
  <?php endif; ?>

  <?php if (isset($_GET['added'])): ?>
    <!-- Landed here straight from the Add form. Say plainly that it worked and
         what the code is, because the code is what gets read out on a call. -->
    <section class="card saved-banner" role="status">
      <div class="card__body">
        <h2 class="saved-banner__title">This property has been saved.</h2>
        <p class="saved-banner__text">
          It is on the list now, and its reference code is
          <span class="ref"><?= e($property['code']) ?></span>. Everything below
          can be changed later.
        </p>
      </div>
    </section>
  <?php endif; ?>

  <div class="console-grid">

    <!-- ================= The file ================= -->
    <div class="console-col">

      <!-- Header: photograph, name, one-line summary -->
      <section class="card" aria-labelledby="property-title">
        <div class="card__head">
          <div>
            <p class="card__eyebrow">
              <?= e($property['code']) ?>
              <span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span>
            </p>
            <h1 class="card__title" id="property-title"><?= e($property['name']) ?></h1>
            <p class="card__note">
              <?= e($property['area']) ?>, <?= e($property['city']) ?> &bull;
              added to our books on <?= e($property['listed']) ?>
            </p>
          </div>
        </div>

        <div class="card__body">
          <img
            class="property-photo"
            src="<?= e(url($property['image'])) ?>"
            alt="Photograph of <?= e($property['name']) ?>"
            loading="lazy">
          <p class="property-summary"><?= e($property['summary']) ?></p>
        </div>
      </section>

      <!-- What this property is -->
      <section class="card" aria-labelledby="facts-title">
        <div class="card__head">
          <div>
            <h2 class="card__title" id="facts-title">What this property is</h2>
            <p class="card__note">The details a buyer will ask you for on the phone.</p>
          </div>
        </div>
        <div class="card__body">
          <dl class="facts">
            <?php foreach (admin_property_facts($property) as $row): ?>
              <div class="facts__row">
                <dt class="facts__label"><?= e($row['label']) ?></dt>
                <dd class="facts__value <?= !empty($row['accent']) ? 'facts__value--accent' : '' ?>">
                  <?= e($row['value']) ?>
                </dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </div>
      </section>

      <!-- Who owns it -->
      <section class="card" aria-labelledby="owner-title">
        <div class="card__head">
          <div>
            <h2 class="card__title" id="owner-title">Who owns it</h2>
            <p class="card__note">Speak to this person before arranging any viewing.</p>
          </div>
        </div>
        <div class="card__body">
          <dl class="facts facts--single">
            <?php foreach (admin_property_owner($property) as $row): ?>
              <div class="facts__row">
                <dt class="facts__label"><?= e($row['label']) ?></dt>
                <dd class="facts__value"><?= e($row['value']) ?></dd>
              </div>
            <?php endforeach; ?>
          </dl>

          <p style="margin-top: var(--s-md)">
            <a class="console-btn console-btn--gold" href="tel:<?= e(str_replace(' ', '', $property['owner_phone'])) ?>">
              Call the owner
              <?= icon('arrow-right', 'icon--sm') ?>
            </a>
          </p>
        </div>
      </section>

      <!-- Buyers who might want this property -->
      <section class="card" aria-labelledby="matches-title">
        <div class="card__head">
          <div>
            <h2 class="card__title" id="matches-title">Buyers who might want this</h2>
            <p class="card__note">
              These are suggestions, not promises. Read the reason, then decide
              whether to call.
            </p>
          </div>
          <span class="card__tag">
            <?= e((string) count($matches)) ?> <?= count($matches) === 1 ? 'buyer' : 'buyers' ?>
          </span>
        </div>

        <div class="card__body">
          <?php if (!$matches): ?>
            <p class="ctable-empty">
              No buyer on our books is looking for a property like this one yet.
            </p>
          <?php else: ?>
            <ul class="match-list">
              <?php foreach ($matches as $match): ?>
                <?php $mstatus = admin_status($match['status']); ?>
                <li class="match">
                  <div class="match__head">
                    <div>
                      <h3 class="match__name"><?= e($match['name']) ?></h3>
                      <p class="match__meta">
                        Their budget: <?= e($match['budget']) ?> &bull;
                        <span class="ref"><?= e($match['reference']) ?></span>
                      </p>
                    </div>
                    <span class="status status--<?= e($mstatus['tone']) ?>">
                      <?= e($mstatus['label']) ?>
                    </span>
                  </div>

                  <p class="match__fit">
                    <span class="match__verdict"><?= e(admin_match_verdict($match['fit'])) ?></span>
                    <span class="match__bar" aria-hidden="true">
                      <span class="match__bar-fill" style="width: <?= e((string) $match['fit']) ?>%"></span>
                    </span>
                    <span class="match__score"><?= e((string) $match['fit']) ?>% fit</span>
                  </p>

                  <p class="match__why"><?= e($match['why']) ?></p>

                  <a class="console-btn" href="<?= e(admin_request_url($match['reference'])) ?>">
                    Open this buyer's request
                    <?= icon('arrow-right', 'icon--sm') ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </section>

      <!-- Papers we hold -->
      <section class="card" aria-labelledby="papers-title">
        <div class="card__head">
          <div>
            <h2 class="card__title" id="papers-title">Papers we hold</h2>
            <p class="card__note">Kept in the locked vault. Press Open to read one.</p>
          </div>
          <span class="card__tag"><?= e((string) count($documents)) ?> files</span>
        </div>
        <div class="card__body">
          <ul class="doc-list">
            <?php foreach ($documents as $doc): ?>
              <li class="doc">
                <span class="doc__badge" aria-hidden="true">PDF</span>
                <span>
                  <span class="doc__name"><?= e($doc['name']) ?></span>
                  <span class="doc__meta"><?= e($doc['meta']) ?></span>
                </span>
                <a class="console-btn doc__action" href="#">
                  Open
                  <span class="visually-hidden"> <?= e($doc['name']) ?></span>
                  <?= icon('arrow-right', 'icon--sm') ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </section>

    </div>

    <!-- ================= Rail ================= -->
    <div class="console-col">

      <!-- Where this property stands -->
      <section class="card" aria-labelledby="standing-title">
        <div class="card__head">
          <h2 class="card__title" id="standing-title">Where this stands</h2>
        </div>
        <div class="card__body">
          <p class="standing">
            <span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span>
          </p>
          <p class="standing__plain"><?= e($status['plain']) ?></p>

          <?php
            // The operator sets this by hand; it is the one thing on this page
            // that changes anything, so it sits directly under the sentence it
            // rewrites rather than at the bottom of the rail.
            $returnUrl = admin_property_url($property['code']);
            include __DIR__ . '/../../includes/property-status-form.php';

            $changeMeta = admin_status_override_meta($property['code']);
          ?>

          <?php if ($changeMeta !== null): ?>
            <p class="standing__changed">
              Last changed by <?= e((string) ($changeMeta['by'] ?? 'the office')) ?>
              on <?= e((string) ($changeMeta['changed'] ?? 'an earlier date')) ?>.
            </p>
          <?php endif; ?>

          <dl class="facts facts--single">
            <div class="facts__row">
              <dt class="facts__label">Asking price</dt>
              <dd class="facts__value"><?= e($property['price']) ?></dd>
            </div>
            <div class="facts__row">
              <dt class="facts__label">Buyers waiting</dt>
              <dd class="facts__value"><?= e((string) $property['matches']) ?></dd>
            </div>
            <div class="facts__row">
              <dt class="facts__label">Reference code</dt>
              <dd class="facts__value"><span class="ref"><?= e($property['code']) ?></span></dd>
            </div>
          </dl>
        </div>
      </section>

      <!-- What to do next -->
      <section class="card" aria-labelledby="next-title">
        <div class="card__head">
          <h2 class="card__title" id="next-title">What to do next</h2>
        </div>
        <div class="card__body">
          <div class="next-action">
            <p class="next-action__text">
              <?php if ($property['status'] === 'checking'): ?>
                The papers are still being checked. Do not arrange a viewing yet.
              <?php elseif ($property['status'] === 'sold'): ?>
                This property is sold. Nothing needs doing.
              <?php elseif ($matches): ?>
                <?= e($matches[0]['name']) ?> is the best match. Call them and
                offer a viewing.
              <?php else: ?>
                No buyer matches this property yet. Nothing needs doing today.
              <?php endif; ?>
            </p>
            <?php
              // Arranging the viewing is the action the sentence above asks
              // for, so it leads — pre-filled with the property and, when we
              // have one, the buyer it is suggesting. Only offered when the
              // property can actually be shown.
              $canShow   = !in_array($property['status'], ['checking', 'sold'], true);
              $bestMatch = $matches[0] ?? null;
              $bookUrl   = route('admin.appointment.new')
                . '?kind=viewing&property=' . rawurlencode($property['code'])
                . ($bestMatch !== null ? '&with=' . rawurlencode($bestMatch['name'])
                    . '&request=' . rawurlencode($bestMatch['reference']) : '');
            ?>

            <?php if ($canShow): ?>
              <a class="console-btn console-btn--gold console-btn--block" href="<?= e($bookUrl) ?>">
                <?= icon('contact-phone', 'icon--sm') ?>
                Arrange a viewing
              </a>
            <?php endif; ?>

            <a class="console-btn <?= $canShow ? 'console-btn--quiet' : 'console-btn--gold' ?> console-btn--block" href="<?= e(route('admin.requests')) ?>">
              Find a buyer for this
              <?= icon('arrow-right', 'icon--sm') ?>
            </a>
          </div>
        </div>
      </section>

      <!-- History -->
      <section class="card" aria-labelledby="history-title">
        <div class="card__head">
          <h2 class="card__title" id="history-title">What has happened so far</h2>
        </div>
        <div class="card__body">
          <ol class="trail">
            <?php foreach (admin_property_activity($property) as $entry): ?>
              <li class="trail__step">
                <span class="trail__text"><?= e($entry['text']) ?></span>
                <span class="trail__time"><?= e($entry['time']) ?></span>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
      </section>

    </div>
  </div>

<?php endif; ?>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
