<?php
/**
 * HS Properties — Appointments | The diary.
 *
 * The same furniture as the Requests inbox and the Properties registry — four
 * counts, a tab strip, one table — so an operator who has learned either of
 * those already knows this page. No filter row: the tabs are the filter, and
 * an appointment has far fewer things worth filtering by than a property.
 *
 * The active view lives in the query string (`?tab=today`) so every view is a
 * shareable link.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';

$tabs = admin_appointment_tabs();
$tab  = isset($_GET['tab']) ? (string) $_GET['tab'] : 'upcoming';

if (!in_array($tab, array_column($tabs, 'key'), true)) {
    $tab = 'upcoming';
}

$rows = admin_tab_appointments($tab);

// A "marked done" or "cancelled" message, when we have just come back from one.
$notice = admin_handle_appointment_post(route('admin.appointments'));

$pageTitle   = 'HS Properties — Appointments';
$adminPage   = 'appointments';
$adminCrumbs = [
    ['label' => 'Admin', 'href' => route('admin.dashboard')],
    ['label' => 'Appointments'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- ================= Page head ================= -->
<section class="page-head">
  <div>
    <h1 class="page-head__title">Appointments</h1>
    <p class="page-head__lede">
      Every meeting we have arranged &mdash; viewings, consultations and
      signings. Open one to see where it is, or press
      <strong>Book an appointment</strong> to arrange a new one.
    </p>
  </div>

  <a class="console-btn console-btn--gold" href="<?= e(route('admin.appointment.new')) ?>">
    Book an appointment
    <?= icon('contact-phone', 'icon--sm') ?>
  </a>
</section>

<?php if ($notice !== null): ?>
  <p class="notice notice--<?= $notice['ok'] ? 'ok' : 'bad' ?>" role="status">
    <?= icon($notice['ok'] ? 'verified' : 'info', 'icon--sm') ?>
    <?= e($notice['text']) ?>
  </p>
<?php endif; ?>

<!-- ================= Summary tiles ================= -->
<section class="tiles tiles--4" aria-label="Appointment summary">
  <?php foreach (admin_appointment_stats() as $tile): ?>
    <article class="tile tile--<?= e($tile['tone']) ?>">
      <h2 class="tile__label"><?= e($tile['label']) ?></h2>
      <p class="tile__value"><?= e($tile['value']) ?></p>
      <p class="tile__note"><?= e($tile['note']) ?></p>
    </article>
  <?php endforeach; ?>
</section>

<!-- ================= The diary ================= -->
<section class="card" aria-labelledby="diary-title">
  <h2 class="visually-hidden" id="diary-title">Appointments</h2>

  <div class="card__head">
    <nav class="tabs" aria-label="Which appointments to show">
      <?php foreach ($tabs as $item): ?>
        <a
          class="tabs__link <?= $item['key'] === $tab ? 'is-current' : '' ?>"
          href="<?= e(route('admin.appointments') . ($item['key'] === 'upcoming' ? '' : '?tab=' . $item['key'])) ?>"
          <?= $item['key'] === $tab ? 'aria-current="true"' : '' ?>>
          <?= e($item['label']) ?>
          <span class="tabs__count"><?= e((string) $item['count']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <?php if (!$rows): ?>
    <p class="ctable-empty">
      <?php if ($tab === 'upcoming'): ?>
        Nothing is booked. <a href="<?= e(route('admin.appointment.new')) ?>">Book an appointment</a>.
      <?php else: ?>
        There is nothing in this view.
        <a href="<?= e(route('admin.appointments')) ?>">Show what is still to come</a>.
      <?php endif; ?>
    </p>
  <?php else: ?>
    <div class="table-wrap">
      <table class="ctable ctable--stack">
        <caption class="visually-hidden">Appointments we have arranged</caption>
        <thead>
          <tr>
            <th scope="col">When</th>
            <th scope="col">What it is</th>
            <th scope="col">Who we are meeting</th>
            <th scope="col">Where</th>
            <th scope="col">How it stands</th>
            <th scope="col">Open it</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $appointment): ?>
            <?php
              $status   = admin_appointment_status($appointment['status']);
              $kind     = admin_appointment_kind($appointment['kind']);
              $property = $appointment['property'] !== null
                ? admin_property($appointment['property'])
                : null;
            ?>
            <tr data-href="<?= e(admin_appointment_url($appointment['ref'])) ?>">
              <td>
                <span class="ctable__name"><?= e(admin_when_words($appointment['date'], $appointment['time'])) ?></span>
                <span class="ctable__sub"><span class="ref"><?= e($appointment['ref']) ?></span></span>
              </td>
              <td data-label="What it is">
                <?= e($kind['label']) ?>
                <?php if ($property !== null): ?>
                  <span class="ctable__sub"><?= e($property['name']) ?></span>
                <?php endif; ?>
              </td>
              <td data-label="Who we are meeting">
                <span class="ctable__name"><?= e($appointment['with']) ?></span>
                <span class="ctable__sub"><?= e($appointment['phone']) ?></span>
              </td>
              <td data-label="Where">
                <?= e($appointment['place']) ?>
                <span class="ctable__sub"><?= e($appointment['desk']) ?> is handling it</span>
              </td>
              <td data-label="How it stands">
                <span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span>
              </td>
              <td>
                <a class="console-btn console-btn--gold" href="<?= e(admin_appointment_url($appointment['ref'])) ?>">
                  Open it
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
    <span>
      Showing <?= e((string) count($rows)) ?> of
      <?= e((string) admin_appointment_counts()['all']) ?> appointments
    </span>
  </div>
</section>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
