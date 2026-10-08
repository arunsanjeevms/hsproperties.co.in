<?php
/**
 * HS Properties — Console form fields.
 *
 * The labelled controls the console's own forms are built from. They were
 * written for "Add a property" and moved here unchanged the moment a second
 * form needed them, so every console form asks its questions the same way:
 * a plain-words label, a line of help with an example underneath it, and the
 * error — when there is one — above the control rather than below, where it
 * would be hidden by a dropdown.
 *
 * Every renderer takes the same two arrays as the form itself: $values (what
 * the operator typed, so a failed save never empties the form) and $errors
 * (keyed by field name).
 */

declare(strict_types=1);

/** One labelled text field, with its error if it has one. */
function cfield(string $name, string $label, string $help, array $values, array $errors, string $type = 'text'): void
{
    $bad = isset($errors[$name]);
    ?>
    <div class="cfield <?= $bad ? 'cfield--bad' : '' ?>">
      <label class="cfield__label" for="f-<?= e($name) ?>"><?= e($label) ?></label>
      <p class="cfield__help" id="h-<?= e($name) ?>"><?= e($help) ?></p>
      <?php if ($bad): ?>
        <p class="cfield__error" id="e-<?= e($name) ?>">
          <strong>Please check this:</strong> <?= e($errors[$name]) ?>
        </p>
      <?php endif; ?>
      <input
        class="cfield__input"
        type="<?= e($type) ?>"
        id="f-<?= e($name) ?>"
        name="<?= e($name) ?>"
        value="<?= e($values[$name]) ?>"
        aria-describedby="h-<?= e($name) ?><?= $bad ? ' e-' . e($name) : '' ?>"
        <?= $bad ? 'aria-invalid="true"' : '' ?>>
    </div>
    <?php
}

/** One labelled dropdown, with its error if it has one. */
function cselect(string $name, string $label, string $help, array $options, array $values, array $errors, string $blank = 'Choose one'): void
{
    $bad = isset($errors[$name]);
    ?>
    <div class="cfield <?= $bad ? 'cfield--bad' : '' ?>">
      <label class="cfield__label" for="f-<?= e($name) ?>"><?= e($label) ?></label>
      <p class="cfield__help" id="h-<?= e($name) ?>"><?= e($help) ?></p>
      <?php if ($bad): ?>
        <p class="cfield__error" id="e-<?= e($name) ?>">
          <strong>Please check this:</strong> <?= e($errors[$name]) ?>
        </p>
      <?php endif; ?>
      <select
        class="cfield__input"
        id="f-<?= e($name) ?>"
        name="<?= e($name) ?>"
        aria-describedby="h-<?= e($name) ?><?= $bad ? ' e-' . e($name) : '' ?>"
        <?= $bad ? 'aria-invalid="true"' : '' ?>>
        <?php if ($blank !== ''): ?>
          <option value=""><?= e($blank) ?></option>
        <?php endif; ?>
        <?php foreach ($options as $value => $text): ?>
          <?php $value = is_int($value) ? $text : $value; ?>
          <option value="<?= e((string) $value) ?>" <?= $values[$name] === $value ? 'selected' : '' ?>>
            <?= e((string) $text) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php
}
