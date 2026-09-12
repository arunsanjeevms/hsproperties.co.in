<?php
/**
 * HS Properties — Requests | Customer Request Inbox.
 *
 * Built to the Stitch screen "HS Properties — Admin Requests Operational
 * Inbox": four summary tiles, the All / Buyers / Sellers / Archived tabs with
 * their counts, the five-filter row, the inbox table and its pagination.
 *
 * The active tab lives in the query string (`?tab=buyers`) so every view is a
 * shareable link and the sidebar's Buyers / Sellers entries point straight at
 * the right tab.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';
require_once __DIR__ . '/../../includes/form-helpers.php';

$tabs = admin_inbox_tabs();
$tab  = isset($_GET['tab']) ? (string) $_GET['tab'] : 'all';

if (!in_array($tab, array_column($tabs, 'key'), true)) {
    $tab = 'all';
}

$rows = admin_tab_requests($tab);

$pageTitle   = 'HS Properties — Requests | Customer Request Inbox';
$adminPage   = $tab === 'buyers' ? 'buyers' : ($tab === 'sellers' ? 'sellers' : 'requests');
$adminCrumbs = [
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Requests Inbox'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- ================= Page head ================= -->
<section class="page-head">
  <div>
    <h1 class="page-head__title">Requests</h1>
    <p class="page-head__lede">
      Every buyer and seller request in one queue. Open one to read the brief,
      or change its status.
    </p>
  </div>

</section>

<!-- ================= Summary tiles ================= -->
<section class="tiles tiles--4" aria-label="Inbox summary">
  <?php foreach (admin_inbox_stats() as $tile): ?>
    <article class="tile tile--<?= e($tile['tone']) ?>">
      <h2 class="tile__label"><?= e($tile['label']) ?></h2>
      <p class="tile__value"><?= e($tile['value']) ?></p>
      <p class="tile__note"><?= e($tile['note']) ?></p>
    </article>
  <?php endforeach; ?>
</section>

<!-- ================= Inbox ================= -->
<section class="card" aria-labelledby="inbox-title">
  <h2 class="visually-hidden" id="inbox-title">Request inbox</h2>

  <div class="card__head">
    <nav class="tabs" aria-label="Inbox views">
      <?php foreach ($tabs as $item): ?>
        <a
          class="tabs__link <?= $item['key'] === $tab ? 'is-current' : '' ?>"
          href="<?= e(route('admin.requests') . ($item['key'] === 'all' ? '' : '?tab=' . $item['key'])) ?>"
          <?= $item['key'] === $tab ? 'aria-current="true"' : '' ?>>
          <?= e($item['label']) ?>
          <span class="tabs__count"><?= e((string) $item['count']) ?></span>
        </a>
      <?php endforeach; ?>
      <span class="tabs__sync">Live &mdash; new requests appear automatically</span>
    </nav>
  </div>

  <!-- Filter row -->
  <form class="card__body filter-row" id="request-filters" method="get" action="<?= e(route('admin.requests')) ?>">
    <input type="hidden" name="tab" value="<?= e($tab) ?>">

    <?php foreach (admin_inbox_filters() as $filter): ?>
      <label class="visually-hidden" for="filter-<?= e($filter['name']) ?>">
        <?= e($filter['label']) ?>
      </label>
      <select
        class="filter-row__select"
        id="filter-<?= e($filter['name']) ?>"
        name="<?= e($filter['name']) ?>">
        <?php foreach ($filter['options'] as $i => $option): ?>
          <?php
            $value    = $i === 0 ? '' : $option;
            $selected = isset($_GET[$filter['name']]) && $_GET[$filter['name']] === $option;
          ?>
          <option value="<?= e($value) ?>" <?= $selected ? 'selected' : '' ?>>
            <?= e($filter['label']) ?>: <?= e($option) ?>
          </option>
        <?php endforeach; ?>
      </select>
    <?php endforeach; ?>

    <a class="console-btn console-btn--quiet" href="<?= e(route('admin.requests') . ($tab === 'all' ? '' : '?tab=' . $tab)) ?>">
      Clear filters
    </a>
    <button class="console-btn" type="submit">Apply filters</button>
  </form>

  <!-- Table -->
  <?php if (!$rows): ?>
    <p class="ctable-empty">
      No requests in this view yet.
      <a href="<?= e(route('admin.requests')) ?>">Return to all requests</a>.
    </p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="ctable">
        <caption class="visually-hidden">Buyer and seller requests</caption>
        <thead>
          <tr>
            <th scope="col">Reference &amp; received</th>
            <th scope="col">Type</th>
            <th scope="col">Customer / property</th>
            <th scope="col">Location</th>
            <th scope="col">Status</th>
            <th scope="col">Handling desk</th>
            <th scope="col">Open</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <?php $status = admin_status($row['status']); ?>
            <tr data-href="<?= e(admin_request_url($row['reference'])) ?>">
              <td>
                <a class="ref" href="<?= e(admin_request_url($row['reference'])) ?>">
                  <?= e($row['reference']) ?>
                </a>
                <span class="ctable__sub"><?= e($row['received']) ?></span>
              </td>
              <td>
                <span class="tag tag--<?= e($row['flow']) ?>">
                  <?= e(admin_flows()[$row['flow']]['label']) ?>
                </span>
              </td>
              <td>
                <span class="ctable__name"><?= e($row['customer']) ?></span>
                <span class="ctable__sub"><?= e(admin_flows()[$row['flow']]['client']) ?></span>
              </td>
              <td><?= e($row['location']) ?></td>
              <td><span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span></td>
              <td><?= e($row['desk']) ?></td>
              <td>
                <span class="ctable__actions">
                  <a class="console-btn" href="<?= e(admin_request_url($row['reference'])) ?>">
                    View
                    <?= icon('arrow-right', 'icon--sm') ?>
                  </a>
                  <button
                    class="ctable__more"
                    type="button"
                    data-status-menu="<?= e($row['reference']) ?>"
                    aria-haspopup="true"
                    aria-expanded="false">
                    &ctdot;
                    <span class="visually-hidden">Change status for <?= e($row['reference']) ?></span>
                  </button>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card__foot">
      <span>
        Showing 1&ndash;<?= e((string) count($rows)) ?> of
        <?= e((string) $tabs[0]['count']) ?> requests
      </span>
      <span class="pager">
        <a class="pager__link" href="#">Previous</a>
        <a class="pager__link is-current" href="#" aria-current="page">1</a>
        <a class="pager__link" href="#">2</a>
        <a class="pager__link" href="#">3</a>
        <a class="pager__link" href="#">Next &rarr;</a>
      </span>
    </div>
  <?php endif; ?>
</section>

<!--
  "Change Status" menu. One menu serves every row: admin.js positions it under
  whichever ⋯ button was pressed, exactly as the blueprint's overlay behaves.
-->
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

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
