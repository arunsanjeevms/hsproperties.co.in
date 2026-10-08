<?php
/**
 * HS Properties — Admin Request Detail View.
 *
 * Built to the Stitch screen of the same name. The panels shown follow the
 * request's own type: a buyer request renders the requirement and budget
 * panels, a seller request the property and sale-expectation panels.
 *
 * Unknown references render a not-found state rather than erroring; no
 * reference at all opens the newest request, as the inbox's top row.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';
require_once __DIR__ . '/../../includes/form-helpers.php';

$reference = isset($_GET['ref']) ? (string) $_GET['ref'] : null;
$request   = $reference === null ? admin_default_request() : admin_request($reference);

/** Which panels to show: always the request's own type. */
$mode = $request['flow'] ?? 'buyer';

$pageTitle   = 'HS Properties — Requests | Customer Request Inbox';
$adminPage   = 'requests';
$adminCrumbs = [
    ['label' => 'Admin',    'href' => route('admin.dashboard')],
    ['label' => 'Requests', 'href' => route('admin.requests')],
    ['label' => $request ? $request['reference'] : 'Unknown'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<?php if (!$request): ?>

  <section class="card">
    <div class="card__body">
      <h1 class="card__title">No request with that reference.</h1>
      <p class="card__note">
        <strong><?= e((string) $reference) ?></strong> is not in the inbox. It may
        have been archived, or the reference mistyped.
      </p>
      <p style="margin-top: var(--s-md)">
        <a class="console-btn console-btn--gold" href="<?= e(route('admin.requests')) ?>">
          <?= icon('arrow-left', 'icon--sm') ?>
          Back to Requests
        </a>
      </p>
    </div>
  </section>

<?php else: ?>

  <?php
    $status   = admin_status($request['status']);
    $dossier  = admin_dossier($request);
    $buyer    = admin_buyer_panels();
    $seller   = admin_seller_panels();
  ?>

  <!-- ================= Back bar ================= -->
  <section class="detail-bar">
    <a class="console-btn console-btn--quiet" href="<?= e(route('admin.requests')) ?>">
      <?= icon('arrow-left', 'icon--sm') ?>
      Back to all requests
    </a>
  </section>

  <div class="console-grid">

    <!-- ================= Dossier column ================= -->
    <div class="console-col">

      <!-- Customer dossier -->
      <section class="card" aria-labelledby="dossier-title">
        <div class="card__head">
          <div>
            <p class="card__eyebrow">
              <?= e($request['reference']) ?>
              <span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span>
              <span class="tag tag--<?= e($mode) ?>"><?= e(admin_flows()[$mode]['label']) ?></span>
            </p>
            <h1 class="card__title" id="dossier-title">Customer dossier</h1>
            <p class="card__note"><?= e($dossier['submitted']) ?></p>
          </div>
          <button
            class="ctable__more"
            type="button"
            data-status-menu="<?= e($request['reference']) ?>"
            aria-haspopup="true"
            aria-expanded="false">
            &ctdot;
            <span class="visually-hidden">Change status</span>
          </button>
        </div>

        <div class="card__body dossier">
          <span class="dossier__avatar" aria-hidden="true"><?= e($dossier['initials']) ?></span>

          <div>
            <h2 class="dossier__name"><?= e($dossier['name']) ?></h2>
            <p class="dossier__sub">
              <?= e($dossier['kind']) ?> &bull; Reference:
              <span class="ref"><?= e($request['reference']) ?></span>
            </p>
          </div>

          <div class="dossier__contacts">
            <span class="dossier__contact">
              <span class="dossier__key" aria-hidden="true">Tel</span>
              <?= e($dossier['phone']) ?>
            </span>
            <span class="dossier__contact">
              <span class="dossier__key" aria-hidden="true">@</span>
              <?= e($dossier['email']) ?>
            </span>
            <a class="console-btn" href="<?= e(route('contact.call')) ?>">
              Call the customer
              <?= icon('arrow-right', 'icon--sm') ?>
            </a>
          </div>
        </div>
      </section>

      <?php if ($mode === 'buyer'): ?>

        <!-- Property requirement -->
        <section class="card" aria-labelledby="req-title">
          <div class="card__head">
            <h2 class="card__title" id="req-title"><?= e($buyer['requirement']['title']) ?></h2>
          </div>
          <div class="card__body">
            <dl class="facts">
              <?php foreach ($buyer['requirement']['rows'] as $row): ?>
                <div class="facts__row">
                  <dt class="facts__label"><?= e($row['label']) ?></dt>
                  <dd class="facts__value"><?= e($row['value']) ?></dd>
                </div>
              <?php endforeach; ?>
            </dl>
          </div>
        </section>

        <!-- Budget & timeline -->
        <section class="card" aria-labelledby="budget-title">
          <div class="card__head">
            <h2 class="card__title" id="budget-title"><?= e($buyer['budget']['title']) ?></h2>
          </div>
          <div class="card__body">
            <dl class="facts">
              <?php foreach ($buyer['budget']['rows'] as $row): ?>
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

        <!-- Priorities -->
        <section class="card" aria-labelledby="priorities-title">
          <div class="card__head">
            <h2 class="card__title" id="priorities-title">What matters most to them</h2>
            <p class="card__note">The priorities the customer ranked highest.</p>
          </div>
          <div class="card__body">
            <p class="chips-row">
              <?php foreach ($buyer['priorities'] as $priority): ?>
                <span class="chip-static"><?= e($priority) ?></span>
              <?php endforeach; ?>
            </p>
          </div>
        </section>

        <!-- Additional requirements -->
        <section class="card" aria-labelledby="extra-title">
          <div class="card__head">
            <h2 class="card__title" id="extra-title">In their own words</h2>
            <p class="card__note">Additional requirements, as submitted.</p>
          </div>
          <div class="card__body">
            <blockquote class="quote">&ldquo;<?= e($buyer['note']) ?>&rdquo;</blockquote>
          </div>
        </section>

      <?php else: ?>

        <!-- Property details -->
        <section class="card" aria-labelledby="details-title">
          <div class="card__head">
            <h2 class="card__title" id="details-title"><?= e($seller['details']['title']) ?></h2>
          </div>
          <div class="card__body">
            <dl class="facts">
              <?php foreach ($seller['details']['rows'] as $row): ?>
                <div class="facts__row">
                  <dt class="facts__label"><?= e($row['label']) ?></dt>
                  <dd class="facts__value"><?= e($row['value']) ?></dd>
                </div>
              <?php endforeach; ?>
            </dl>
          </div>
        </section>

        <!-- Sale expectations -->
        <section class="card" aria-labelledby="sale-title">
          <div class="card__head">
            <h2 class="card__title" id="sale-title"><?= e($seller['expectations']['title']) ?></h2>
          </div>
          <div class="card__body">
            <dl class="facts">
              <?php foreach ($seller['expectations']['rows'] as $row): ?>
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

      <?php endif; ?>

      <!-- Documents provided -->
      <section class="card" aria-labelledby="docs-title">
        <div class="card__head">
          <div>
            <h2 class="card__title" id="docs-title">Documents provided</h2>
            <p class="card__note">Held in the encrypted vault.</p>
          </div>
          <span class="card__tag">4 files</span>
        </div>
        <div class="card__body">
          <ul class="doc-list">
            <?php foreach (admin_detail_documents() as $doc): ?>
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

      <!-- Internal notes -->
      <section class="card" aria-labelledby="notes-title">
        <div class="card__head">
          <div>
            <h2 class="card__title" id="notes-title">Internal notes</h2>
            <p class="card__note">Team only &mdash; the customer never sees these.</p>
          </div>
          <span class="card__tag">Private</span>
        </div>

        <form class="card__body" method="post" action="<?= e(admin_request_url($request['reference'])) ?>">
          <?php foreach (admin_internal_notes() as $note): ?>
            <div class="note-item">
              <p class="note-item__head">
                <span class="note-item__author"><?= e($note['author']) ?></span>
                <span class="note-item__time"><?= e($note['time']) ?></span>
              </p>
              <p class="note-item__text"><?= e($note['text']) ?></p>
            </div>
          <?php endforeach; ?>

          <label class="visually-hidden" for="note">Add a note</label>
          <textarea
            class="note-field" id="note" name="note" rows="3"
            placeholder="Add an internal note for the team…"></textarea>

          <p style="margin-top: var(--s-sm)">
            <button class="console-btn console-btn--dark" type="submit">Save note</button>
          </p>
        </form>
      </section>

    </div>

    <!-- ================= Status rail ================= -->
    <div class="console-col">

      <!-- Current status -->
      <section class="card" aria-labelledby="status-title">
        <div class="card__head">
          <h2 class="card__title" id="status-title">Current status</h2>
        </div>
        <div class="card__body">
          <dl class="facts facts--single">
            <?php foreach (admin_detail_status($request) as $row): ?>
              <div class="facts__row">
                <dt class="facts__label"><?= e($row['label']) ?></dt>
                <dd class="facts__value">
                  <?php if (!empty($row['pill'])): ?>
                    <span class="status status--<?= e($status['tone']) ?>"><?= e($row['value']) ?></span>
                  <?php else: ?>
                    <?= e($row['value']) ?>
                  <?php endif; ?>
                </dd>
              </div>
            <?php endforeach; ?>
          </dl>
        </div>
      </section>

      <!-- Next action -->
      <section class="card" aria-labelledby="next-title">
        <div class="card__head">
          <h2 class="card__title" id="next-title">Next action</h2>
        </div>
        <div class="card__body">
          <div class="next-action">
            <p class="next-action__text">
              This request is still new. Read the brief, then mark it reviewed.
            </p>
            <button
              class="console-btn console-btn--gold console-btn--block"
              type="button"
              data-status-menu="<?= e($request['reference']) ?>"
              aria-haspopup="true"
              aria-expanded="false">
              Mark as reviewed
              <?= icon('arrow-right', 'icon--sm') ?>
            </button>

            <?php
              // Meeting them is the step after reading the brief, so booking
              // arrives with the client and their request already filled in.
              $bookUrl = route('admin.appointment.new')
                . '?kind=' . ($request['flow'] === 'seller' ? 'seller' : 'buyer')
                . '&with=' . rawurlencode($request['customer'])
                . '&request=' . rawurlencode($request['reference']);
            ?>
            <a class="console-btn console-btn--quiet console-btn--block" href="<?= e($bookUrl) ?>">
              <?= icon('contact-phone', 'icon--sm') ?>
              Book an appointment
            </a>
          </div>
        </div>
      </section>

      <!-- Recent activity -->
      <section class="card" aria-labelledby="activity-title">
        <div class="card__head">
          <h2 class="card__title" id="activity-title">Request history</h2>
        </div>
        <div class="card__body">
          <ol class="trail">
            <?php foreach (admin_detail_activity() as $entry): ?>
              <li class="trail__step">
                <span class="trail__text"><?= e($entry['text']) ?></span>
                <span class="trail__time"><?= e($entry['time']) ?></span>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
        <div class="card__foot">
          <a class="console-link" href="#">
            View all activity
            <?= icon('arrow-right', 'icon--sm') ?>
          </a>
        </div>
      </section>

    </div>
  </div>

  <!-- Shared "Change Status" menu and confirmation toast -->
  <div class="card status-menu" id="status-menu" hidden>
    <p class="card__eyebrow status-menu__head">Change Status</p>
    <ul class="assign">
      <?php foreach (admin_status_menu() as $option): ?>
        <li>
          <button class="assign__option" type="button">
            <?= e($option) ?>
            <?= icon('check', 'icon--sm') ?>
          </button>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <p class="console-toast" id="status-toast" role="status" hidden>
    <?= icon('verified', 'icon--sm') ?>
    Status updated successfully
  </p>

<?php endif; ?>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
