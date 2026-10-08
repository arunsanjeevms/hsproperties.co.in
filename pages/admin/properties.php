<?php
/**
 * HS Properties — Properties | Registry list.
 *
 * The same shape as the Requests inbox (tiles, tabs, filter row, table) so an
 * operator who has learned one page already knows this one.
 *
 * Written for staff without much screen experience: every column heading is a
 * question in plain words, each row carries one full-width "View details"
 * button rather than an icon, and the page opens with a sentence saying what
 * the list is for.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';
require_once __DIR__ . '/../../includes/form-helpers.php';

$tabs = admin_property_tabs();
$tab  = isset($_GET['tab']) ? (string) $_GET['tab'] : 'all';

if (!in_array($tab, array_column($tabs, 'key'), true)) {
    $tab = 'all';
}

// A status change is posted back to this same view, so the operator stays
// where they were. Runs before any output: it redirects on success.
$returnUrl = route('admin.properties') . ($tab === 'all' ? '' : '?tab=' . $tab);
$notice    = admin_handle_status_post($returnUrl);

$rows = admin_tab_properties($tab);

$pageTitle   = 'HS Properties — Properties';
$adminPage   = 'properties';
$adminCrumbs = [
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Properties'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- ================= Page head ================= -->
<section class="page-head">
  <div>
    <h1 class="page-head__title">Properties</h1>
    <p class="page-head__lede">
      Every property we have been asked to sell. Change what is happening to
      one straight from the list, or press <strong>View details</strong> to see
      the full file, the papers we hold, and the buyers who might want it.
    </p>
  </div>

  <a class="console-btn console-btn--gold" href="<?= e(route('admin.property.new')) ?>">
    Add a property
    <?= icon('check', 'icon--sm') ?>
  </a>
</section>

<?php if ($notice !== null): ?>
  <p class="notice notice--<?= $notice['ok'] ? 'ok' : 'bad' ?>" role="status">
    <?= icon($notice['ok'] ? 'verified' : 'info', 'icon--sm') ?>
    <?= e($notice['text']) ?>
  </p>
<?php endif; ?>

<!-- ================= Summary tiles ================= -->
<section class="tiles tiles--4" aria-label="Property summary">
  <?php foreach (admin_property_stats() as $tile): ?>
    <article class="tile tile--<?= e($tile['tone']) ?>">
      <h2 class="tile__label"><?= e($tile['label']) ?></h2>
      <p class="tile__value"><?= e($tile['value']) ?></p>
      <p class="tile__note"><?= e($tile['note']) ?></p>
    </article>
  <?php endforeach; ?>
</section>

<!-- ================= The list ================= -->
<section class="card" aria-labelledby="registry-title">
  <h2 class="visually-hidden" id="registry-title">Property list</h2>

  <div class="card__head">
    <nav class="tabs" aria-label="Which properties to show">
      <?php foreach ($tabs as $item): ?>
        <a
          class="tabs__link <?= $item['key'] === $tab ? 'is-current' : '' ?>"
          href="<?= e(route('admin.properties') . ($item['key'] === 'all' ? '' : '?tab=' . $item['key'])) ?>"
          <?= $item['key'] === $tab ? 'aria-current="true"' : '' ?>>
          <?= e($item['label']) ?>
          <span class="tabs__count"><?= e((string) $item['count']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <!-- Filter row -->
  <form class="card__body filter-row" method="get" action="<?= e(route('admin.properties')) ?>">
    <input type="hidden" name="tab" value="<?= e($tab) ?>">

    <?php foreach (admin_property_filters() as $filter): ?>
      <label class="visually-hidden" for="pfilter-<?= e($filter['name']) ?>">
        <?= e($filter['label']) ?>
      </label>
      <select
        class="filter-row__select"
        id="pfilter-<?= e($filter['name']) ?>"
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

    <a class="console-btn console-btn--quiet" href="<?= e(route('admin.properties') . ($tab === 'all' ? '' : '?tab=' . $tab)) ?>">
      Clear filters
    </a>
    <button class="console-btn" type="submit">Apply filters</button>
  </form>

  <!-- Table -->
  <?php if (!$rows): ?>
    <p class="ctable-empty">
      There are no properties in this view.
      <a href="<?= e(route('admin.properties')) ?>">Show all properties</a>.
    </p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="ctable ctable--stack">
        <caption class="visually-hidden">Properties on our books</caption>
        <thead>
          <tr>
            <th scope="col">Property</th>
            <th scope="col">Asking price</th>
            <th scope="col">What is happening &mdash; change it here</th>
            <th scope="col">Buyers waiting</th>
            <th scope="col">Open the file</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $property): ?>
            <?php $status = admin_property_status($property['status']); ?>
            <tr data-href="<?= e(admin_property_url($property['code'])) ?>">
              <td>
                <span class="plot">
                  <img
                    class="plot__thumb"
                    src="<?= e(url($property['image'])) ?>"
                    alt=""
                    width="64" height="48" loading="lazy">
                  <span>
                    <span class="ctable__name"><?= e($property['name']) ?></span>
                    <span class="ctable__sub">
                      <?= e($property['kind']) ?> &bull; <?= e($property['beds']) ?> &bull;
                      <?= e($property['area']) ?>, <?= e($property['city']) ?>
                    </span>
                    <span class="ctable__sub">
                      <span class="ref"><?= e($property['code']) ?></span>
                    </span>
                  </span>
                </span>
              </td>
              <td data-label="Asking price"><span class="plot__price"><?= e($property['price']) ?></span></td>
              <td data-label="What is happening">
                <span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span>
                <?php include __DIR__ . '/../../includes/property-status-form.php'; ?>
              </td>
              <td data-label="Buyers waiting">
                <?php if ($property['matches'] > 0): ?>
                  <?= e((string) $property['matches']) ?>
                  <?= $property['matches'] === 1 ? 'buyer' : 'buyers' ?>
                <?php else: ?>
                  <span class="plot__none">None yet</span>
                <?php endif; ?>
              </td>
              <td>
                <a class="console-btn console-btn--gold" href="<?= e(admin_property_url($property['code'])) ?>">
                  View details
                  <?= icon('arrow-right', 'icon--sm') ?>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <div class="card__foot">
    <span>Showing <?= e((string) count($rows)) ?> of <?= e((string) admin_property_counts()['all']) ?> properties</span>
    <span class="pager">
      <a class="pager__link" href="<?= e(route('admin.properties')) ?>">Previous</a>
      <a class="pager__link is-current" href="<?= e(route('admin.properties')) ?>">1</a>
      <a class="pager__link" href="<?= e(route('admin.properties')) ?>">2</a>
      <a class="pager__link" href="<?= e(route('admin.properties')) ?>">Next</a>
    </span>
  </div>
</section>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
