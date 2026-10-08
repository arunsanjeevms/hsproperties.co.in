<?php
/**
 * HS Properties — Appointments | Book an appointment.
 *
 * Built on the same shape as "Add a property": one page, one column, short
 * sections, one Save button, and a mistake that reloads with the error above
 * the field AND a summary at the top, keeping everything already typed.
 *
 * Most bookings do not start here. An operator reading "call them and offer a
 * viewing" on a property file presses a button there and arrives with the
 * property, the buyer and their number already filled in, leaving only the day
 * and the time to choose. Those arrive as `?property=…&with=…&phone=…&request=…`.
 *
 * Two mistakes are refused outright, because they are the two this console can
 * actually catch: a day that has already passed, and a slot where the same
 * person is already booked.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';
require_once __DIR__ . '/../../includes/console-fields.php';

$kinds  = admin_appointment_kinds();
$places = admin_appointment_places();
$times  = admin_appointment_times();

/** Everyone who could be handling it. One desk per operator, for now. */
$desks = ['Vikram K', 'Priya S', 'Anil R'];

/** Properties, as code => the name an operator would recognise. */
$properties = ['' => 'No particular property'];
foreach (admin_properties() as $row) {
    $properties[$row['code']] = $row['name'] . ' (' . $row['code'] . ')';
}

/** Fields the form will not book without, each with the verb that fits it. */
$required = [
    'kind'  => 'choose what the appointment is for',
    'with'  => 'type the name of the person we are meeting',
    'date'  => 'choose the day',
    'time'  => 'choose the time',
    'place' => 'choose where you are meeting',
];

$errors = [];

/* Whatever the operator typed — or was handed by the page that sent them here
   — so neither a failed save nor a pre-fill is ever lost. */
$values = [
    'kind'     => (string) ($_GET['kind'] ?? 'viewing'),
    'with'     => (string) ($_GET['with'] ?? ''),
    'phone'    => (string) ($_GET['phone'] ?? ''),
    'date'     => date('Y-m-d', strtotime('+1 day')),
    'time'     => '11:00',
    'place'    => 'At the property',
    'property' => (string) ($_GET['property'] ?? ''),
    'request'  => (string) ($_GET['request'] ?? ''),
    'desk'     => admin_operator()['name'],
    'note'     => '',
];

// A pre-filled property we do not have is not worth arguing about: drop it.
if ($values['property'] !== '' && !isset($properties[$values['property']])) {
    $values['property'] = '';
}

if (!isset($kinds[$values['kind']])) {
    $values['kind'] = 'viewing';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $default) {
        $values[$field] = trim((string) ($_POST[$field] ?? $default));
    }

    foreach ($required as $field => $what) {
        if ($values[$field] === '') {
            $errors[$field] = 'Please ' . $what . '.';
        }
    }

    if (!isset($kinds[$values['kind']]) && $values['kind'] !== '') {
        $errors['kind'] = 'Choose one of the kinds in the list.';
    }

    if ($values['time'] !== '' && !isset($times[$values['time']])) {
        $errors['time'] = 'Choose one of the times in the list.';
    }

    if ($values['place'] !== '' && !in_array($values['place'], $places, true)) {
        $errors['place'] = 'Choose one of the places in the list.';
    }

    if ($values['desk'] !== '' && !in_array($values['desk'], $desks, true)) {
        $values['desk'] = admin_operator()['name'];
    }

    if ($values['property'] !== '' && !isset($properties[$values['property']])) {
        $errors['property'] = 'That property is not on our books. Choose one from the list.';
    }

    // A day that has already gone cannot be booked — it can only be recorded,
    // and that is not what this form is for.
    if ($values['date'] !== '') {
        $stamp = strtotime($values['date']);

        if ($stamp === false) {
            $errors['date'] = 'That is not a date. Use the calendar to pick a day.';
        } elseif (date('Y-m-d', $stamp) < date('Y-m-d')) {
            $errors['date'] = 'That day has already passed. Choose today or a day after it.';
        }
    }

    // The mistake worth catching: the same person in two places at once.
    if (!$errors) {
        $clash = admin_appointment_clash($values['date'], $values['time'], $values['desk']);

        if ($clash !== null) {
            $errors['time'] = $values['desk'] . ' is already booked then — '
                . $clash['ref'] . ', with ' . $clash['with']
                . '. Choose another time, or hand this one to somebody else.';
        }
    }

    if (!$errors) {
        $ref = admin_next_appointment_ref();

        $appointment = [
            'ref'      => $ref,
            'kind'     => $values['kind'],
            'status'   => 'booked',
            'date'     => $values['date'],
            'time'     => $values['time'],
            'with'     => $values['with'],
            'phone'    => $values['phone'],
            'property' => $values['property'] !== '' ? $values['property'] : null,
            'request'  => $values['request'] !== '' ? $values['request'] : null,
            'place'    => $values['place'],
            'desk'     => $values['desk'],
            'note'     => $values['note'] !== ''
                ? $values['note']
                : 'Nothing else was written down about this meeting.',
            'outcome'  => '',
            'booked'   => date('d M Y'),
        ];

        if (admin_save_appointment($appointment)) {
            header('Location: ' . admin_appointment_url($ref) . '&booked=1', true, 303);
            exit;
        }

        $errors['save'] = 'The appointment could not be saved. The console could not '
            . 'write to its data folder. Nothing you typed has been lost — tell '
            . 'whoever looks after this system, then press Book it again.';
    }
}

$pageTitle   = 'HS Properties — Book an appointment';
$adminPage   = 'appointments';
$adminCrumbs = [
    ['label' => 'Admin',        'href' => route('admin.dashboard')],
    ['label' => 'Appointments', 'href' => route('admin.appointments')],
    ['label' => 'Book an appointment'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- ================= Back bar ================= -->
<section class="detail-bar">
  <a class="console-btn console-btn--quiet" href="<?= e(route('admin.appointments')) ?>">
    <?= icon('arrow-left', 'icon--sm') ?>
    Back to all appointments
  </a>
</section>

<section class="page-head">
  <div>
    <h1 class="page-head__title">Book an appointment</h1>
    <p class="page-head__lede">
      Fill in what you have agreed with the client and press
      <strong>Book it</strong> at the bottom. You can mark it done, or cancel
      it, afterwards.
    </p>
  </div>
</section>

<?php if ($errors): ?>
  <!-- The summary: what went wrong, all in one place, before the form. -->
  <section class="card cform-alert" role="alert" aria-labelledby="alert-title">
    <div class="card__body">
      <h2 class="cform-alert__title" id="alert-title">
        <?php if (isset($errors['save'])): ?>
          The appointment was not booked.
        <?php else: ?>
          <?= e((string) count($errors)) ?>
          <?= count($errors) === 1 ? 'answer needs' : 'answers need' ?> checking.
        <?php endif; ?>
      </h2>
      <?php if (isset($errors['save'])): ?>
        <p class="cform-alert__text"><?= e($errors['save']) ?></p>
      <?php else: ?>
        <p class="cform-alert__text">
          Nothing has been booked yet. The questions below are marked in red.
          Everything you typed is still here.
        </p>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>

<form class="cform" method="post" action="<?= e(route('admin.appointment.new')) ?>" novalidate>
  <input type="hidden" name="request" value="<?= e($values['request']) ?>">

  <!-- ================= What and who ================= -->
  <section class="card" aria-labelledby="what-title">
    <div class="card__head">
      <div>
        <h2 class="card__title" id="what-title">1. What it is, and who with</h2>
        <p class="card__note">The two things the client will ask you to confirm.</p>
      </div>
    </div>

    <div class="card__body">
      <?php
        $kindOptions = [];
        foreach ($kinds as $key => $kind) {
            $kindOptions[$key] = $kind['label'];
        }
      ?>
      <div class="cform-grid">
        <?php cselect('kind', 'What is this appointment for?', 'A viewing is showing a property. A consultation is sitting down to talk.', $kindOptions, $values, $errors, ''); ?>
        <?php cfield('with', 'Who are we meeting?', 'Their full name, as you would say it on the phone. For example: Meera Singhal', $values, $errors); ?>
        <?php cfield('phone', 'Their phone number', 'So whoever attends can ring them on the day. For example: +91 98450 22190', $values, $errors, 'tel'); ?>
        <?php cselect('property', 'Which property is it about?', 'Leave this alone if the meeting is not about one particular property.', $properties, $values, $errors, ''); ?>
      </div>
    </div>
  </section>

  <!-- ================= When and where ================= -->
  <section class="card" aria-labelledby="when-title">
    <div class="card__head">
      <div>
        <h2 class="card__title" id="when-title">2. When and where</h2>
        <p class="card__note">Use the day and time you have agreed out loud with them.</p>
      </div>
    </div>

    <div class="card__body">
      <div class="cform-grid">
        <?php cfield('date', 'Which day?', 'Pick the day from the calendar. It cannot be a day that has passed.', $values, $errors, 'date'); ?>
        <?php cselect('time', 'What time?', 'The office works in half hours, 9 in the morning to 7 in the evening.', $times, $values, $errors, ''); ?>
        <?php cselect('place', 'Where are you meeting?', 'Whatever you told the client. They will expect to hear the same back.', $places, $values, $errors, ''); ?>
        <?php cselect('desk', 'Who is handling it?', 'The person who will be there. They cannot be booked twice at one time.', $desks, $values, $errors, ''); ?>
      </div>
    </div>
  </section>

  <!-- ================= Anything else ================= -->
  <section class="card" aria-labelledby="note-title">
    <div class="card__head">
      <div>
        <h2 class="card__title" id="note-title">3. Anything else to remember</h2>
        <p class="card__note">
          What to bring, where to park, who else is coming. Whoever attends will
          read this, and it may not be you.
        </p>
      </div>
    </div>

    <div class="card__body">
      <div class="cfield">
        <label class="cfield__label" for="f-note">Notes for whoever attends</label>
        <p class="cfield__help" id="h-note">
          Leave it empty if there is nothing to say.
        </p>
        <textarea
          class="cfield__input cfield__input--tall"
          id="f-note"
          name="note"
          rows="3"
          aria-describedby="h-note"><?= e($values['note']) ?></textarea>
      </div>
    </div>
  </section>

  <!-- ================= Save ================= -->
  <section class="card">
    <div class="card__body cform-foot">
      <button class="console-btn console-btn--gold cform-save" type="submit">
        Book it
        <?= icon('check', 'icon--sm') ?>
      </button>
      <a class="console-btn console-btn--quiet" href="<?= e(route('admin.appointments')) ?>">
        Cancel and go back
      </a>
    </div>
  </section>
</form>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
