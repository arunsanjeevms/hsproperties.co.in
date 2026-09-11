<?php
/**
 * HS Properties — Form control renderers.
 *
 * Each control is a real <input> wrapped in its <label>. The input is visually
 * hidden but focusable, so keyboard selection, arrow-key movement within a
 * radio group, and assistive-technology semantics all work natively, and the
 * controls still submit correctly with JavaScript disabled. The selected
 * styling is driven by CSS `:has(:checked)` rather than a JS class.
 *
 * Shared by the seller intake now and the buyer intake later.
 */

declare(strict_types=1);

/**
 * Selectable tile with a title, supporting note, and a champagne pip.
 *
 * @param array $option ['value' => string, 'label' => string, 'note' => string]
 */
function option_card(string $name, array $option, bool $checked = false, string $type = 'radio'): string
{
    ob_start(); ?>
<label class="option">
  <input
    class="option__input"
    type="<?= e($type) ?>"
    name="<?= e($name) ?>"
    value="<?= e($option['value']) ?>"
    <?= $checked ? 'checked' : '' ?>>
  <span class="option__title"><?= e($option['label']) ?></span>
  <?php if (!empty($option['note'])): ?>
    <span class="option__note"><?= e($option['note']) ?></span>
  <?php endif; ?>
  <span class="option__pip" aria-hidden="true"></span>
</label>
<?php
    return (string) ob_get_clean();
}

/**
 * Tile with a leading radio ring — used where the choice is a formal
 * declaration (ownership structure, mandate structure) rather than a preset.
 */
function option_marked(string $name, array $option, bool $checked = false, string $type = 'radio'): string
{
    ob_start(); ?>
<label class="option option--marked">
  <input
    class="option__input"
    type="<?= e($type) ?>"
    name="<?= e($name) ?>"
    value="<?= e($option['value']) ?>"
    <?= $checked ? 'checked' : '' ?>>
  <span class="option__ring" aria-hidden="true"></span>
  <span class="option__body">
    <span class="option__title"><?= e($option['label']) ?></span>
    <?php if (!empty($option['note'])): ?>
      <span class="option__note"><?= e($option['note']) ?></span>
    <?php endif; ?>
  </span>
</label>
<?php
    return (string) ob_get_clean();
}

/**
 * Compact single-line choice within a segmented control.
 */
function segment(string $name, array $option, bool $checked = false): string
{
    ob_start(); ?>
<label class="segment">
  <input
    class="option__input"
    type="radio"
    name="<?= e($name) ?>"
    value="<?= e($option['value']) ?>"
    <?= $checked ? 'checked' : '' ?>>
  <?= e($option['label']) ?>
</label>
<?php
    return (string) ob_get_clean();
}

/**
 * Multi-select document chip carrying a staged/optional status line.
 */
function doc_chip(string $name, array $option, bool $checked = false): string
{
    ob_start(); ?>
<label class="chip">
  <input
    class="option__input"
    type="checkbox"
    name="<?= e($name) ?>[]"
    value="<?= e($option['value']) ?>"
    <?= $checked ? 'checked' : '' ?>>
  <span class="chip__tick" aria-hidden="true"><?= icon('check', 'icon--sm') ?></span>
  <span class="chip__title"><?= e($option['label']) ?></span>
  <span class="chip__status"><?= e($option['status']) ?></span>
</label>
<?php
    return (string) ob_get_clean();
}

/**
 * A staged document row.
 *
 * @param string $variant 'editable' shows View/remove controls (Step 02);
 *                        'summary' shows the document category (Step 03).
 */
function document_row(array $doc, string $variant = 'editable'): string
{
    ob_start(); ?>
<li class="file">
  <span class="file__badge" aria-hidden="true"><?= e($doc['kind']) ?></span>
  <span class="file__body">
    <span class="file__name"><?= e($doc['name']) ?></span>
    <span class="file__meta">
      <?= e($doc['size']) ?>
      <span aria-hidden="true">&bull;</span>
      <span class="file__state"><?= e($doc['state']) ?></span>
    </span>
  </span>

  <?php if ($variant === 'summary'): ?>
    <span class="file__tag"><?= e($doc['category']) ?></span>
  <?php else: ?>
    <span class="file__actions">
      <a class="file__link" href="#">
        View<span class="visually-hidden"> <?= e($doc['name']) ?></span>
      </a>
      <button class="file__remove" type="button">
        <?= icon('trash', 'icon--sm') ?>
        <span class="visually-hidden">Remove <?= e($doc['name']) ?></span>
      </button>
    </span>
  <?php endif; ?>
</li>
<?php
    return (string) ob_get_clean();
}
