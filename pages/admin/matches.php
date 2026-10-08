<?php
/**
 * HS Properties — Matches | Who has been paired with what.
 *
 * A reading page. It exists so an operator can see, at a glance, which buyer
 * has been put against which property, how close the two are, and whether
 * anyone has spoken to them yet. Nothing on this page changes anything: acting
 * on a match happens on the buyer's request or on the property itself, and
 * both are one click away from every card.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';

$matches = admin_matches();

$pageTitle   = 'HS Properties — Matches';
$adminPage   = 'matches';
$adminCrumbs = [
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Matches'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- ================= Page head ================= -->
<section class="page-head">
  <div>
    <h1 class="page-head__title">Matches</h1>
    <p class="page-head__lede">
      Buyers we have paired with a property, and why. This page is for reading
      only &mdash; to do something about a match, open the buyer's request or
      the property file from the card.
    </p>
  </div>
</section>

<!-- ================= The pairings ================= -->
<section class="card" aria-labelledby="matches-title">
  <div class="card__head">
    <h2 class="card__title" id="matches-title">
      <?= e((string) count($matches)) ?>
      <?= count($matches) === 1 ? 'pairing' : 'pairings' ?> on the books
    </h2>
  </div>

  <div class="card__body">
    <?php if (!$matches): ?>
      <p class="ctable-empty">No buyer has been paired with a property yet.</p>
    <?php else: ?>
      <ul class="match-list">
        <?php foreach ($matches as $match): ?>
          <?php $status = admin_status($match['status']); ?>
          <li class="match">

            <div class="match__head">
              <div>
                <h3 class="match__name"><?= e($match['buyer']) ?></h3>
                <p class="match__meta">
                  Paired <?= e($match['suggested']) ?> &bull;
                  <span class="ref"><?= e($match['id']) ?></span>
                </p>
              </div>
              <span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span>
            </div>

            <!-- The two sides of the pairing, side by side. -->
            <div class="match__pair">
              <div class="match__side">
                <p class="match__side-label">The buyer wants</p>
                <p class="match__side-value"><?= e($match['wants']) ?></p>
                <p class="match__side-note">Budget <?= e($match['budget']) ?></p>
                <p class="match__side-note">
                  <a class="ref" href="<?= e(admin_request_url($match['reference'])) ?>">
                    <?= e($match['reference']) ?>
                  </a>
                </p>
              </div>

              <div class="match__side">
                <p class="match__side-label">The property</p>
                <p class="match__side-value"><?= e($match['property']) ?></p>
                <p class="match__side-note">
                  <?= e($match['price']) ?> &bull; <?= e($match['where']) ?>
                </p>
                <p class="match__side-note">
                  <a class="ref" href="<?= e(admin_property_url($match['code'])) ?>">
                    <?= e($match['code']) ?>
                  </a>
                </p>
              </div>
            </div>

            <p class="match__fit">
              <span class="match__verdict"><?= e(admin_match_verdict($match['fit'])) ?></span>
              <span class="match__bar" aria-hidden="true">
                <span class="match__bar-fill" style="width: <?= e((string) $match['fit']) ?>%"></span>
              </span>
              <span class="match__score"><?= e((string) $match['fit']) ?>% fit</span>
            </p>

            <p class="match__why"><?= e($match['why']) ?></p>

            <p class="match__note">
              <span class="match__note-label">Where it stands:</span>
              <?= e($match['note']) ?>
            </p>

            <p class="match__links">
              <?php if ($match['status'] !== 'completed'): ?>
                <a
                  class="console-btn console-btn--gold"
                  href="<?= e(route('admin.appointment.new')
                    . '?kind=viewing'
                    . '&property=' . rawurlencode($match['code'])
                    . '&with=' . rawurlencode($match['buyer'])
                    . '&request=' . rawurlencode($match['reference'])) ?>">
                  <?= icon('contact-phone', 'icon--sm') ?>
                  Arrange a viewing
                </a>
              <?php endif; ?>
              <a class="console-btn" href="<?= e(admin_request_url($match['reference'])) ?>">
                Open the buyer's request
                <?= icon('arrow-right', 'icon--sm') ?>
              </a>
              <a class="console-btn" href="<?= e(admin_property_url($match['code'])) ?>">
                Open the property file
                <?= icon('arrow-right', 'icon--sm') ?>
              </a>
            </p>

          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
