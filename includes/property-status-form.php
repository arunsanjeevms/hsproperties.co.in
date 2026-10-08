<?php
/**
 * HS Properties — the manual status control.
 *
 * A property's status changes for reasons no rule can guess: the owner rings
 * up, a buyer withdraws, the papers come back. So an operator sets it by hand.
 *
 * The current status is already on the page as the coloured pill, so this is
 * worded as the change rather than repeating it: the list offers only the
 * statuses the property is not already at. It is a plain form — no menu, no
 * scripting needed — and Save only appears once a choice has been made.
 *
 * Expects:
 *   $property  array   the property being changed
 *   $returnUrl string  where to come back to after saving
 */

$formId = 'status-form-' . preg_replace('/[^A-Za-z0-9_-]/', '', (string) $property['code']);
?>
<form class="status-change" method="post" action="<?= e($returnUrl) ?>" id="<?= e($formId) ?>">
  <input type="hidden" name="code" value="<?= e($property['code']) ?>">

  <label class="visually-hidden" for="<?= e($formId) ?>-select">
    Change the status of <?= e($property['name']) ?>
  </label>

  <select
    class="status-change__select"
    id="<?= e($formId) ?>-select"
    name="property_status"
    required
    data-status-select>
    <option value="" selected>Change to&hellip;</option>
    <?php foreach (admin_property_statuses() as $key => $status): ?>
      <?php if ($key === $property['status']): ?>
        <?php continue; ?>
      <?php endif; ?>
      <option value="<?= e($key) ?>"><?= e($status['label']) ?></option>
    <?php endforeach; ?>
  </select>

  <button class="console-btn status-change__save" type="submit" data-status-save>
    Save
  </button>
</form>
