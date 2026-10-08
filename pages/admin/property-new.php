<?php
/**
 * HS Properties — Properties | Add a property.
 *
 * One page, one column, four short sections, one Save button. Written for
 * staff who are not confident with software:
 *
 *   • every field says in plain words what to type, with an example;
 *   • the reference code is generated for them rather than asked for;
 *   • a mistake reloads the page with the error above the field AND a summary
 *     at the top, and keeps everything they already typed;
 *   • nothing is lost silently — if the file cannot be written the page says
 *     so instead of pretending the property was saved.
 *
 * On success the operator is redirected to the new property's own page, so the
 * result of pressing Save is the thing they just made.
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/admin-data.php';
require_once __DIR__ . '/../../includes/form-helpers.php';
require_once __DIR__ . '/../../includes/console-fields.php';

/** Fields the form will not save without. */
$required = [
    'name'        => 'the name of the property',
    'kind'        => 'the type of property',
    'beds'        => 'the number of bedrooms',
    'size'        => 'the size in square feet',
    'area'        => 'the area',
    'city'        => 'the city',
    'price'       => 'the asking price',
    'owner'       => 'the owner\'s name',
    'owner_phone' => 'the owner\'s phone number',
];

$kinds    = ['Villa', 'Apartment', 'Penthouse', 'Plot', 'Farmhouse'];
$cities   = ['Bengaluru', 'Chennai'];
$bedrooms = ['1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK', '6 BHK or more'];

/** Photographs already in the site's library, until uploads are built. */
$photos = [
    'assets/images/hero-villa.jpg'                  => 'Villa at dusk',
    'assets/images/villa-terrace.jpg'               => 'Villa with terrace',
    'assets/images/property-oceanfront-estate.jpg'  => 'House by the sea',
    'assets/images/property-louvered-penthouse.jpg' => 'Penthouse block',
    'assets/images/property-sandstone-pavilion.jpg' => 'Sandstone apartment',
];

$errors = [];
$saved  = null;

/* Whatever the operator typed, so a failed save never empties the form. */
$values = [
    'name'        => '',
    'kind'        => '',
    'beds'        => '',
    'size'        => '',
    'area'        => '',
    'city'        => '',
    'price'       => '',
    'status'      => 'checking',
    'owner'       => '',
    'owner_phone' => '',
    'owner_email' => '',
    'image'       => 'assets/images/hero-villa.jpg',
    'summary'     => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $field => $default) {
        $values[$field] = trim((string) ($_POST[$field] ?? $default));
    }

    foreach ($required as $field => $what) {
        if ($values[$field] === '') {
            $errors[$field] = 'Please type ' . $what . '.';
        }
    }

    // The two numbers are asked for as plain numbers, so they are checked as
    // plain numbers and the message says what a good answer looks like.
    if ($values['size'] !== '' && !preg_match('/^\d[\d,]*$/', $values['size'])) {
        $errors['size'] = 'Type the size as a number only, like 4200.';
    }

    if ($values['price'] !== '' && !preg_match('/^\d+(\.\d+)?$/', $values['price'])) {
        $errors['price'] = 'Type the price in crores as a number, like 3.2.';
    }

    if ($values['owner_email'] !== '' && !filter_var($values['owner_email'], FILTER_VALIDATE_EMAIL)) {
        $errors['owner_email'] = 'That does not look like an email address. Check it, or leave it empty.';
    }

    if (!in_array($values['kind'], $kinds, true) && $values['kind'] !== '') {
        $errors['kind'] = 'Choose one of the types in the list.';
    }

    if (!in_array($values['city'], $cities, true) && $values['city'] !== '') {
        $errors['city'] = 'Choose one of the cities in the list.';
    }

    if (!isset(admin_property_statuses()[$values['status']])) {
        $values['status'] = 'checking';
    }

    if (!isset($photos[$values['image']])) {
        $values['image'] = 'assets/images/hero-villa.jpg';
    }

    if (!$errors) {
        $code = admin_next_property_code();

        $property = [
            'code'        => $code,
            'name'        => $values['name'],
            'kind'        => $values['kind'],
            'city'        => $values['city'],
            'area'        => $values['area'],
            'price'       => '₹' . rtrim(rtrim($values['price'], '0'), '.') . ' Cr',
            'size'        => number_format((float) str_replace(',', '', $values['size'])) . ' sq ft',
            'beds'        => $values['beds'],
            'status'      => $values['status'],
            'owner'       => $values['owner'],
            'owner_phone' => $values['owner_phone'],
            'owner_email' => $values['owner_email'],
            'listed'      => date('d M Y'),
            'matches'     => 0,
            'image'       => $values['image'],
            'summary'     => $values['summary'] !== ''
                ? $values['summary']
                : 'No description has been written for this property yet.',
        ];

        if (admin_store_property($property)) {
            header('Location: ' . admin_property_url($code) . '&added=1');
            exit;
        }

        $errors['save'] = 'The property could not be saved. The console could not '
            . 'write to its data folder. Nothing you typed has been lost — tell '
            . 'whoever looks after this system, then press Save again.';
    }
}

$pageTitle   = 'HS Properties — Add a property';
$adminPage   = 'properties';
$adminCrumbs = [
    ['label' => 'Admin',      'href' => route('admin.dashboard')],
    ['label' => 'Properties', 'href' => route('admin.properties')],
    ['label' => 'Add a property'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- ================= Back bar ================= -->
<section class="detail-bar">
  <a class="console-btn console-btn--quiet" href="<?= e(route('admin.properties')) ?>">
    <?= icon('arrow-left', 'icon--sm') ?>
    Back to all properties
  </a>
</section>

<section class="page-head">
  <div>
    <h1 class="page-head__title">Add a property</h1>
    <p class="page-head__lede">
      Fill in what you know and press <strong>Save this property</strong> at the
      bottom. You can change any of it later. The reference code is created for
      you &mdash; this one will be
      <span class="ref"><?= e(admin_next_property_code()) ?></span>.
    </p>
  </div>
</section>

<?php if ($errors): ?>
  <section class="card cform-alert" role="alert" aria-labelledby="alert-title">
    <div class="card__body">
      <h2 class="cform-alert__title" id="alert-title">
        <?php if (isset($errors['save'])): ?>
          The property was not saved.
        <?php else: ?>
          <?= e((string) count($errors)) ?>
          <?= count($errors) === 1 ? 'answer needs' : 'answers need' ?> checking.
        <?php endif; ?>
      </h2>
      <?php if (isset($errors['save'])): ?>
        <p class="cform-alert__text"><?= e($errors['save']) ?></p>
      <?php else: ?>
        <p class="cform-alert__text">
          Nothing has been saved yet. The questions below are marked in red.
          Everything you typed is still here.
        </p>
      <?php endif; ?>
    </div>
  </section>
<?php endif; ?>

<form class="cform" method="post" action="<?= e(route('admin.property.new')) ?>" novalidate>

  <!-- 1 — the property -->
  <section class="card" aria-labelledby="s1-title">
    <div class="card__head">
      <div>
        <h2 class="card__title" id="s1-title">1. What is the property?</h2>
        <p class="card__note">The basics a buyer asks for first.</p>
      </div>
    </div>
    <div class="card__body">
      <div class="cform-grid">
        <?php cfield('name', 'Name of the property', 'For example: Whitefield Executive Villa', $values, $errors); ?>
        <?php cselect('kind', 'Type of property', 'Is it a villa, an apartment, something else?', $kinds, $values, $errors); ?>
        <?php cselect('beds', 'Bedrooms', 'How many bedrooms does it have?', $bedrooms, $values, $errors); ?>
        <?php cfield('size', 'Size in square feet', 'Numbers only. For example: 4200', $values, $errors); ?>
      </div>
    </div>
  </section>

  <!-- 2 — where it is -->
  <section class="card" aria-labelledby="s2-title">
    <div class="card__head">
      <div>
        <h2 class="card__title" id="s2-title">2. Where is it?</h2>
        <p class="card__note">The area first, then the city.</p>
      </div>
    </div>
    <div class="card__body">
      <div class="cform-grid">
        <?php cfield('area', 'Area or locality', 'For example: Whitefield, or Alwarpet', $values, $errors); ?>
        <?php cselect('city', 'City', 'Which city is it in?', $cities, $values, $errors); ?>
      </div>
    </div>
  </section>

  <!-- 3 — price and owner -->
  <section class="card" aria-labelledby="s3-title">
    <div class="card__head">
      <div>
        <h2 class="card__title" id="s3-title">3. Price and owner</h2>
        <p class="card__note">Who we call, and what they are asking for.</p>
      </div>
    </div>
    <div class="card__body">
      <div class="cform-grid">
        <?php cfield('price', 'Asking price, in crores', 'Numbers only. Type 3.2 for three crore twenty lakh.', $values, $errors); ?>
        <?php cfield('owner', 'Owner\'s name', 'The person or company selling it', $values, $errors); ?>
        <?php cfield('owner_phone', 'Owner\'s phone number', 'For example: +91 98450 12849', $values, $errors, 'tel'); ?>
        <?php cfield('owner_email', 'Owner\'s email (not compulsory)', 'Leave this empty if you do not have it', $values, $errors, 'email'); ?>
      </div>
    </div>
  </section>

  <!-- 4 — status, photograph, description -->
  <section class="card" aria-labelledby="s4-title">
    <div class="card__head">
      <div>
        <h2 class="card__title" id="s4-title">4. Anything else</h2>
        <p class="card__note">You can leave all of this and come back to it.</p>
      </div>
    </div>
    <div class="card__body">
      <div class="cform-grid">
        <?php
          $statusOptions = [];
          foreach (admin_property_statuses() as $key => $meta) {
              $statusOptions[$key] = $meta['label'];
          }
          cselect('status', 'What is happening with it?', 'New properties usually start as "Papers being checked".', $statusOptions, $values, $errors, '');
          cselect('image', 'Photograph', 'Pick the closest picture for now. Real photographs are added later.', $photos, $values, $errors, '');
        ?>
      </div>

      <div class="cfield">
        <label class="cfield__label" for="f-summary">Describe it in a sentence or two</label>
        <p class="cfield__help" id="h-summary">
          Write it the way you would describe it to a buyer on the phone.
        </p>
        <textarea
          class="cfield__input cfield__input--tall"
          id="f-summary" name="summary" rows="4"
          aria-describedby="h-summary"><?= e($values['summary']) ?></textarea>
      </div>
    </div>
  </section>

  <!-- Save -->
  <section class="card">
    <div class="card__body cform-foot">
      <button class="console-btn console-btn--gold cform-save" type="submit">
        Save this property
        <?= icon('check', 'icon--sm') ?>
      </button>
      <a class="console-btn console-btn--quiet" href="<?= e(route('admin.properties')) ?>">
        Cancel and go back
      </a>
    </div>
  </section>

</form>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
