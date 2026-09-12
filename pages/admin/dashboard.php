<?php
/**
 * HS Properties — Operations Center (Admin Overview).
 *
 * Built to the Stitch screen "HS Properties — Admin Operations Dashboard
 * (Sidebar Edition)": greeting row, five metric tiles, Needs Your Attention,
 * Pipeline funnels, Shortcuts, Recent Requests, Today's schedule,
 * and the immutable activity log.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';
require_once __DIR__ . '/../../includes/form-helpers.php';

$pageTitle   = 'HS Properties — Operations Center (Admin Overview)';
$adminPage   = 'overview';
$adminCrumbs = [
    ['label' => 'Operations Console'],
    ['label' => 'Fiduciary Control'],
    ['label' => 'Overview'],
];
$greeting = admin_greeting();

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- ================= Greeting ================= -->
<section class="page-head">
  <div>
    <p class="page-head__eyebrow">
      <?= e($greeting['eyebrow']) ?>
      <span class="page-head__systems"><?= e($greeting['systems']) ?></span>
    </p>
    <h1 class="page-head__title"><?= e($greeting['title']) ?></h1>
    <p class="page-head__lede"><?= e($greeting['lede']) ?></p>
  </div>

  <p class="page-head__today">
    <span class="page-head__today-label">Today</span>
    <span class="page-head__today-day"><?= e($greeting['day']) ?></span>
    <span class="page-head__today-time"><?= e($greeting['time']) ?></span>
  </p>
</section>

<!-- ================= Metric tiles ================= -->
<section class="tiles" aria-label="Desk metrics">
  <?php foreach (admin_metrics() as $tile): ?>
    <article class="tile tile--<?= e($tile['tone']) ?>">
      <h2 class="tile__label"><?= e($tile['label']) ?></h2>
      <p class="tile__value"><?= e($tile['value']) ?></p>
      <p class="tile__note"><?= e($tile['note']) ?></p>
    </article>
  <?php endforeach; ?>
</section>

<div class="console-grid">

  <!-- ================= Main column ================= -->
  <div class="console-col">

    <!-- Needs your attention -->
    <section class="card" aria-labelledby="attention-title">
      <div class="card__head">
        <div>
          <h2 class="card__title" id="attention-title">Needs your attention</h2>
          <p class="card__note">
            Mandates, enquiries and verifications waiting on the desk right now.
          </p>
        </div>
        <span class="card__tag"><?= e((string) count(admin_attention())) ?> need action</span>
      </div>

      <ul class="attention">
        <?php foreach (admin_attention() as $item): ?>
          <?php
            $flagClass = $item['priority'] === 'HIGH' ? 'flag--high'
                       : ($item['priority'] === 'FOLLOW-UP' ? 'flag--followup' : '');
          ?>
          <li class="attention__item">
            <div>
              <p class="attention__meta">
                <span class="flag <?= e($flagClass) ?>"><?= e($item['priority']) ?></span>
                <span class="tag tag--<?= e($item['flow']) ?>">
                  <?= e(admin_flows()[$item['flow']]['label']) ?>
                </span>
                <a class="ref" href="<?= e(admin_request_url($item['reference'])) ?>">
                  <?= e($item['reference']) ?>
                </a>
              </p>
              <h3 class="attention__title"><?= e($item['title']) ?></h3>
              <p class="attention__text"><?= e($item['text']) ?></p>
              <p class="attention__foot">
                <?= e($item['meta']) ?>
                <span aria-hidden="true">&bull;</span>
                <?= e($item['received']) ?>
              </p>
            </div>

            <span class="attention__action">
              <a class="console-btn" href="<?= e(admin_request_url($item['reference'])) ?>">
                <?= e($item['action']) ?>
                <?= icon('arrow-right', 'icon--sm') ?>
              </a>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="card__foot">
        <a class="console-link" href="<?= e(route('admin.requests')) ?>">
          View all 18 requests in the registry queue
          <?= icon('arrow-right', 'icon--sm') ?>
        </a>
      </div>
    </section>

    <!-- Recent requests -->
    <section class="card" aria-labelledby="recent-title">
      <div class="card__head">
        <div>
          <h2 class="card__title" id="recent-title">Recent requests</h2>
          <p class="card__note">
            Buyer preferences and seller mandates as they arrive.
          </p>
        </div>
        <a class="console-link" href="<?= e(route('admin.requests')) ?>">
          View all requests
          <?= icon('arrow-right', 'icon--sm') ?>
        </a>
      </div>

      <div class="table-wrap">
        <table class="ctable">
          <caption class="visually-hidden">Most recent requests</caption>
          <thead>
            <tr>
              <th scope="col">Reference</th>
              <th scope="col">Type</th>
              <th scope="col">Client</th>
              <th scope="col">Location</th>
              <th scope="col">Status</th>
              <th scope="col">Updated</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (admin_recent_requests() as $row): ?>
              <tr data-href="<?= e(admin_request_url($row['reference'])) ?>">
                <td>
                  <a class="ref" href="<?= e(admin_request_url($row['reference'])) ?>">
                    <?= e($row['reference']) ?>
                  </a>
                </td>
                <td>
                  <span class="tag tag--<?= e($row['flow']) ?>">
                    <?= e(admin_flows()[$row['flow']]['label']) ?>
                  </span>
                </td>
                <td><?= e($row['client']) ?></td>
                <td><?= e($row['location']) ?></td>
                <td><?= e($row['desk']) ?> desk</td>
                <td><span class="status status--<?= e($row['tone']) ?>"><?= e($row['status']) ?></span></td>
                <td><?= e($row['updated']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="card__foot">
        <span>Showing the 5 most recent of 48 active requests</span>
        <span class="pager">
          <a class="pager__link" href="<?= e(route('admin.requests')) ?>">Previous</a>
          <a class="pager__link" href="<?= e(route('admin.requests')) ?>">Next</a>
        </span>
      </div>
    </section>

  </div>

  <!-- ================= Side column ================= -->
  <div class="console-col">

    <!-- Pipeline -->
    <section class="card" aria-labelledby="funnel-title">
      <div class="card__head">
        <div>
          <h2 class="card__title" id="funnel-title">Active pipeline</h2>
          <p class="card__note">How many mandates sit at each stage.</p>
        </div>
      </div>

      <div class="card__body">
        <?php foreach (admin_pipeline() as $funnel): ?>
          <?php $peak = max(array_column($funnel['stages'], 'count')); ?>
          <div class="funnel">
            <div class="funnel__head">
              <h3 class="funnel__title"><?= e($funnel['title']) ?></h3>
              <span class="funnel__note"><?= e($funnel['note']) ?></span>
            </div>

            <ul class="funnel__stages">
              <?php foreach ($funnel['stages'] as $stage): ?>
                <li class="funnel__stage">
                  <span class="funnel__label"><?= e($stage['label']) ?></span>
                  <span class="funnel__bar">
                    <span
                      class="funnel__fill"
                      style="width: <?= e((string) (int) round($stage['count'] / $peak * 100)) ?>%"></span>
                  </span>
                  <span class="funnel__count"><?= e((string) $stage['count']) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>

      <p class="confidential">
        <?= icon('lock', 'icon--sm') ?>
        Mandates remain strictly confidential. No programmatic broadcasting to
        third-party portals.
      </p>
    </section>

    <!-- Shortcuts -->
    <section class="card" aria-labelledby="shortcuts-title">
      <div class="card__head">
        <div>
          <h2 class="card__title" id="shortcuts-title">Quick actions</h2>
        </div>
      </div>

      <div class="card__body">
        <div class="shortcuts">
          <?php foreach (admin_quick_actions() as $action): ?>
            <?php if ($action['route'] !== null): ?>
              <a class="console-btn" href="<?= e(route($action['route'])) ?>">
                <?= e($action['label']) ?>
              </a>
            <?php else: ?>
              <span class="console-btn console-btn--quiet" aria-disabled="true">
                <?= e($action['label']) ?>
              </span>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Today's schedule -->
    <section class="card" aria-labelledby="agenda-title">
      <div class="card__head">
        <div>
          <h2 class="card__title" id="agenda-title">Today&rsquo;s schedule</h2>
          <p class="card__note">5 sessions booked.</p>
        </div>
        <a class="console-link" href="#">
          Open the calendar
          <?= icon('arrow-right', 'icon--sm') ?>
        </a>
      </div>

      <div class="card__body">
        <ul class="agenda">
          <?php foreach (admin_schedule() as $session): ?>
            <li class="agenda__item">
              <span class="agenda__time"><?= e($session['time']) ?></span>
              <span>
                <span class="agenda__title">
                  <?= e($session['title']) ?>
                  <span class="status status--<?= $session['state'] === 'Confirmed' ? 'complete' : 'review' ?>">
                    <?= e($session['state']) ?>
                  </span>
                </span>
                <span class="agenda__note"><?= e($session['note']) ?></span>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="card__foot">
        <span class="console-btn console-btn--quiet" aria-disabled="true">
          Schedule a session
        </span>
      </div>
    </section>

    <!-- Recent activity -->
    <section class="card" aria-labelledby="audit-title">
      <div class="card__head">
        <div>
          <h2 class="card__title" id="audit-title">Recent activity</h2>
          <p class="card__note">
            Encrypted audit log of every partner change.
          </p>
        </div>
        <span class="card__tag">Cannot be edited</span>
      </div>

      <div class="card__body">
        <ul class="audit">
          <?php foreach (admin_activity() as $entry): ?>
            <li class="audit__item">
              <span class="audit__time"><?= e($entry['time']) ?></span>
              <span>
                <span class="audit__actor"><?= e($entry['actor']) ?></span>
                <?= e($entry['text']) ?>
                <a class="ref" href="<?= e(admin_request_url($entry['reference'])) ?>">
                  <?= e($entry['reference']) ?>
                </a>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="card__foot">
        <a class="console-link" href="#">
          Download the signed audit trail (PDF)
          <?= icon('arrow-right', 'icon--sm') ?>
        </a>
      </div>
    </section>

  </div>
</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
