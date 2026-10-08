<?php
/**
 * HS Properties — Appointments | One appointment.
 *
 * Opened from the diary, or landed on straight after booking. Same two-column
 * console shape as the request and property detail views: the appointment
 * itself on the left, the things you act on in the right rail.
 *
 * The rail carries the only two things that happen to an appointment after it
 * is made — it happened, or it did not — and both ask for a sentence saying
 * what came of it, because "Done" on its own tells the next person nothing.
 *
 * An unknown reference renders a not-found state rather than erroring; no
 * reference at all opens the next appointment due.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';

$ref = isset($_GET['ref']) ? (string) $_GET['ref'] : null;

// Marking done or cancelling posts back here. Runs before any output, and
// before the appointment is read, so the page always shows what was just saved.
$notice = $ref === null ? null : admin_handle_appointment_post(admin_appointment_url($ref));

$appointment = $ref === null ? admin_default_appointment() : admin_appointment($ref);

$pageTitle   = 'HS Properties — Appointments';
$adminPage   = 'appointments';
$adminCrumbs = [
    ['label' => 'Admin',        'href' => route('admin.dashboard')],
    ['label' => 'Appointments', 'href' => route('admin.appointments')],
    ['label' => $appointment ? $appointment['ref'] : 'Unknown'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<?php if (!$appointment): ?>

  <section class="card">
    <div class="card__body">
      <h1 class="card__title">We could not find that appointment.</h1>
      <p class="card__note">
        <strong><?= e((string) $ref) ?></strong> is not in the diary. It may have
        been removed, or the reference may have been typed wrongly.
      </p>
      <p style="margin-top: var(--s-md)">
        <a class="console-btn console-btn--gold" href="<?= e(route('admin.appointments')) ?>">
          <?= icon('arrow-left', 'icon--sm') ?>
          Back to all appointments
        </a>
      </p>
    </div>
  </section>

<?php else: ?>

  <?php
    $status   = admin_appointment_status($appointment['status']);
    $kind     = admin_appointment_kind($appointment['kind']);
    $property = $appointment['property'] !== null ? admin_property($appointment['property']) : null;
    $isOpen   = $appointment['status'] === 'booked';
  ?>

  <!-- ================= Back bar ================= -->
  <section class="detail-bar">
    <a class="console-btn console-btn--quiet" href="<?= e(route('admin.appointments')) ?>">
      <?= icon('arrow-left', 'icon--sm') ?>
      Back to all appointments
    </a>
  </section>

  <?php if ($notice !== null): ?>
    <p class="notice notice--<?= $notice['ok'] ? 'ok' : 'bad' ?>" role="status">
      <?= icon($notice['ok'] ? 'verified' : 'info', 'icon--sm') ?>
      <?= e($notice['text']) ?>
    </p>
  <?php endif; ?>

  <?php if (isset($_GET['booked'])): ?>
    <!-- Landed here straight from the booking form. Say plainly that it worked
         and what the reference is, because that is what gets read out. -->
    <section class="card saved-banner" role="status">
      <div class="card__body">
        <h2 class="saved-banner__title">This appointment is booked.</h2>
        <p class="saved-banner__text">
          It is in the diary now, and its reference is
          <span class="ref"><?= e($appointment['ref']) ?></span>. Nothing has been
          sent to the client &mdash; ring them to confirm.
        </p>
      </div>
    </section>
  <?php endif; ?>

  <div class="console-grid">

    <!-- ================= The appointment ================= -->
    <div class="console-col">

      <section class="card" aria-labelledby="appointment-title">
        <div class="card__head">
          <div>
            <p class="card__eyebrow">
              <span class="ref"><?= e($appointment['ref']) ?></span>
              <span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span>
            </p>
            <h1 class="card__title" id="appointment-title"><?= e($kind['label']) ?></h1>
            <p class="card__note">
              <?= e(admin_when_words($appointment['date'], $appointment['time'])) ?>
              &bull; <?= e($appointment['place']) ?>
            </p>
          </div>
        </div>

        <div class="card__body">
          <p class="card__note"><?= e($kind['note']) ?></p>
        </div>
      </section>

      <!-- Who we are meeting -->
      <section class="card" aria-labelledby="who-title">
        <div class="card__head">
          <div>
            <h2 class="card__title" id="who-title">Who we are meeting</h2>
            <p class="card__note">Ring them the day before to remind them.</p>
          </div>
        </div>

        <div class="card__body">
          <dl class="facts">
            <div class="facts__row">
              <dt class="facts__label">Name</dt>
              <dd class="facts__value"><?= e($appointment['with']) ?></dd>
            </div>
            <div class="facts__row">
              <dt class="facts__label">Phone</dt>
              <dd class="facts__value">
                <?php if ($appointment['phone'] !== ''): ?>
                  <a href="tel:<?= e(str_replace(' ', '', $appointment['phone'])) ?>">
                    <?= e($appointment['phone']) ?>
                  </a>
                <?php else: ?>
                  No number was written down
                <?php endif; ?>
              </dd>
            </div>
            <div class="facts__row">
              <dt class="facts__label">Their request</dt>
              <dd class="facts__value">
                <?php if ($appointment['request'] !== null): ?>
                  <a class="ref" href="<?= e(admin_request_url($appointment['request'])) ?>">
                    <?= e($appointment['request']) ?>
                  </a>
                <?php else: ?>
                  Not linked to a request
                <?php endif; ?>
              </dd>
            </div>
            <div class="facts__row">
              <dt class="facts__label">Handled by</dt>
              <dd class="facts__value"><?= e($appointment['desk']) ?></dd>
            </div>
          </dl>
        </div>
      </section>

      <!-- The property, when there is one -->
      <?php if ($property !== null): ?>
        <section class="card" aria-labelledby="property-title">
          <div class="card__head">
            <div>
              <h2 class="card__title" id="property-title">The property</h2>
              <p class="card__note">What the meeting is about.</p>
            </div>
            <a class="console-link" href="<?= e(admin_property_url($property['code'])) ?>">
              Open the property file
              <?= icon('arrow-right', 'icon--sm') ?>
            </a>
          </div>

          <div class="card__body">
            <span class="plot">
              <img
                class="plot__thumb"
                src="<?= e(url($property['image'])) ?>"
                alt=""
                width="64" height="48" loading="lazy">
              <span>
                <span class="ctable__name"><?= e($property['name']) ?></span>
                <span class="ctable__sub">
                  <?= e($property['price']) ?> &bull; <?= e($property['beds']) ?> &bull;
                  <?= e($property['area']) ?>, <?= e($property['city']) ?>
                </span>
                <span class="ctable__sub"><span class="ref"><?= e($property['code']) ?></span></span>
              </span>
            </span>
          </div>
        </section>
      <?php endif; ?>

      <!-- Notes -->
      <section class="card" aria-labelledby="notes-title">
        <div class="card__head">
          <div>
            <h2 class="card__title" id="notes-title">Notes for whoever attends</h2>
            <p class="card__note">Written when the appointment was booked.</p>
          </div>
        </div>

        <div class="card__body">
          <p class="card__note"><?= e($appointment['note']) ?></p>
        </div>
      </section>

      <?php if (!$isOpen && $appointment['outcome'] !== ''): ?>
        <section class="card" aria-labelledby="outcome-title">
          <div class="card__head">
            <div>
              <h2 class="card__title" id="outcome-title">
                <?= $appointment['status'] === 'done' ? 'What came of it' : 'Why it did not happen' ?>
              </h2>
            </div>
          </div>

          <div class="card__body">
            <p class="card__note"><?= e($appointment['outcome']) ?></p>
          </div>
        </section>
      <?php endif; ?>

    </div>

    <!-- ================= The rail ================= -->
    <div class="console-col">

      <section class="card" aria-labelledby="standing-title">
        <div class="card__head">
          <h2 class="card__title" id="standing-title">Where this stands</h2>
        </div>

        <div class="card__body">
          <p class="standing">
            <span class="status status--<?= e($status['tone']) ?>"><?= e($status['label']) ?></span>
          </p>
          <p class="standing__plain"><?= e($status['plain']) ?></p>

          <dl class="facts facts--single">
            <div class="facts__row">
              <dt class="facts__label">When</dt>
              <dd class="facts__value"><?= e(admin_when_words($appointment['date'], $appointment['time'])) ?></dd>
            </div>
            <div class="facts__row">
              <dt class="facts__label">Where</dt>
              <dd class="facts__value"><?= e($appointment['place']) ?></dd>
            </div>
            <div class="facts__row">
              <dt class="facts__label">Booked on</dt>
              <dd class="facts__value"><?= e($appointment['booked']) ?></dd>
            </div>
          </dl>
        </div>
      </section>

      <!-- What to do next: the only two things that happen to an appointment -->
      <?php if ($isOpen): ?>
        <section class="card" aria-labelledby="close-title">
          <div class="card__head">
            <div>
              <h2 class="card__title" id="close-title">Afterwards</h2>
              <p class="card__note">
                Once the meeting has happened &mdash; or you know it will not
                &mdash; say so here, so nobody else chases it.
              </p>
            </div>
          </div>

          <div class="card__body">
            <form class="appt-close" method="post" action="<?= e(admin_appointment_url($appointment['ref'])) ?>">
              <input type="hidden" name="ref" value="<?= e($appointment['ref']) ?>">

              <div class="cfield">
                <label class="cfield__label" for="f-outcome">What happened?</label>
                <p class="cfield__help" id="h-outcome">
                  One or two sentences. If you are cancelling, say why.
                </p>
                <textarea
                  class="cfield__input cfield__input--tall"
                  id="f-outcome"
                  name="outcome"
                  rows="3"
                  aria-describedby="h-outcome"></textarea>
              </div>

              <p class="appt-close__actions">
                <button
                  class="console-btn console-btn--gold console-btn--block"
                  type="submit"
                  name="appointment_action"
                  value="done">
                  <?= icon('check', 'icon--sm') ?>
                  Mark as done
                </button>
                <button
                  class="console-btn console-btn--quiet console-btn--block"
                  type="submit"
                  name="appointment_action"
                  value="cancelled">
                  Cancel this appointment
                </button>
              </p>
            </form>
          </div>
        </section>
      <?php else: ?>
        <section class="card" aria-labelledby="closed-title">
          <div class="card__head">
            <h2 class="card__title" id="closed-title">Afterwards</h2>
          </div>

          <div class="card__body">
            <div class="next-action">
              <p class="next-action__text">
                <?= $appointment['status'] === 'done'
                  ? 'This meeting has happened. Nothing else needs doing here.'
                  : 'This meeting was cancelled. Book another one if they still want to come.' ?>
              </p>
              <a class="console-btn console-btn--gold console-btn--block"
                 href="<?= e(route('admin.appointment.new')
                   . '?with=' . rawurlencode($appointment['with'])
                   . '&phone=' . rawurlencode($appointment['phone'])
                   . '&kind=' . rawurlencode($appointment['kind'])
                   . ($appointment['property'] !== null ? '&property=' . rawurlencode($appointment['property']) : '')
                   . ($appointment['request'] !== null ? '&request=' . rawurlencode($appointment['request']) : '')) ?>">
                Book another with them
                <?= icon('arrow-right', 'icon--sm') ?>
              </a>
            </div>
          </div>
        </section>
      <?php endif; ?>

      <!-- What has happened so far -->
      <section class="card" aria-labelledby="trail-title">
        <div class="card__head">
          <h2 class="card__title" id="trail-title">What has happened so far</h2>
        </div>

        <div class="card__body">
          <ol class="trail">
            <?php foreach (admin_appointment_activity($appointment) as $entry): ?>
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
