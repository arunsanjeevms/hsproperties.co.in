<?php
/**
 * HS Properties — Portfolio Properties Management.
 */

declare(strict_types=1);

$activePage = 'properties';
$adminTitle = 'Portfolio Properties';

require_once __DIR__ . '/../backend/bootstrap.php';
require_once __DIR__ . '/../backend/models/Property.php';
require_once __DIR__ . '/../backend/controllers/PropertyController.php';

$model = new Property();
$ctrl  = new PropertyController();

$action = $_GET['action'] ?? 'list';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        $ctrl->store();
    } elseif ($action === 'edit' && $id) {
        $ctrl->update($id);
    } elseif ($action === 'delete' && $id) {
        $ctrl->destroy($id);
    }
}

$editItem = ($action === 'edit' && $id) ? $model->find($id) : null;

// List view data
$search = $_GET['search'] ?? '';
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$list   = $model->getAll(['search' => $search], $page, 20);

include __DIR__ . '/header.php';
?>

<?php if ($action === 'create' || ($action === 'edit' && $editItem)): ?>
  <!-- Create / Edit Form -->
  <div style="margin-bottom: 20px;">
    <a href="<?= e(url('admin/properties.php')) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">&larr; Back to properties</a>
  </div>

  <div class="admin-card" style="max-width: 800px;">
    <div class="admin-card__header">
      <h2 class="admin-card__title"><?= $action === 'create' ? 'Add New Property' : 'Edit Property: ' . e($editItem['title']) ?></h2>
    </div>

    <div style="padding: 24px;">
      <form method="post" enctype="multipart/form-data" action="<?= e(url('admin/properties.php?action=' . $action . ($id ? '&id=' . $id : ''))) ?>">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div class="admin-form-group">
            <label for="title">Property Title *</label>
            <input type="text" id="title" name="title" class="admin-input" required value="<?= e($editItem['title'] ?? '') ?>" placeholder="The Sandstone Pavilion Villa">
          </div>

          <div class="admin-form-group">
            <label for="reference">Reference Code</label>
            <input type="text" id="reference" name="reference" class="admin-input" value="<?= e($editItem['reference'] ?? '') ?>" placeholder="#BLR-402">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div class="admin-form-group">
            <label for="location">Location *</label>
            <input type="text" id="location" name="location" class="admin-input" required value="<?= e($editItem['location'] ?? '') ?>" placeholder="Whitefield, Bengaluru">
          </div>

          <div class="admin-form-group">
            <label for="type">Property Type *</label>
            <select name="type" id="type" class="admin-select" required>
              <option value="Independent Estate" <?= ($editItem['type'] ?? '') === 'Independent Estate' ? 'selected' : '' ?>>Independent Estate</option>
              <option value="Urban Duplex" <?= ($editItem['type'] ?? '') === 'Urban Duplex' ? 'selected' : '' ?>>Urban Duplex</option>
              <option value="Coastal Retreat" <?= ($editItem['type'] ?? '') === 'Coastal Retreat' ? 'selected' : '' ?>>Coastal Retreat</option>
              <option value="Penthouse" <?= ($editItem['type'] ?? '') === 'Penthouse' ? 'selected' : '' ?>>Penthouse</option>
              <option value="Commercial Landmark" <?= ($editItem['type'] ?? '') === 'Commercial Landmark' ? 'selected' : '' ?>>Commercial Landmark</option>
            </select>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
          <div class="admin-form-group">
            <label for="status">Status Badge</label>
            <input type="text" id="status" name="status" class="admin-input" value="<?= e($editItem['status'] ?? 'Sold & Concluded') ?>" placeholder="Sold & Concluded / Landmark Sale">
          </div>

          <div class="admin-form-group">
            <label for="mandate">Mandate Type</label>
            <input type="text" id="mandate" name="mandate" class="admin-input" value="<?= e($editItem['mandate'] ?? 'Buyer Representation & Acquisition') ?>" placeholder="Buyer Representation & Acquisition">
          </div>
        </div>

        <div class="admin-form-group">
          <label for="spec">Architectural Specification</label>
          <input type="text" id="spec" name="spec" class="admin-input" value="<?= e($editItem['spec'] ?? '') ?>" placeholder="4 BHK Cantilevered Residence · 8,200 sq.ft Grounds">
        </div>

        <div class="admin-form-group">
          <label for="summary">Editorial Summary</label>
          <textarea id="summary" name="summary" rows="4" class="admin-textarea" placeholder="Discreet acquisition advisory completed for private family office..."><?= e($editItem['summary'] ?? '') ?></textarea>
        </div>

        <div class="admin-form-group">
          <label for="image">Featured Image</label>
          <?php if (!empty($editItem['image'])): ?>
            <div style="margin-bottom: 8px;">
              <img src="<?= e(url($editItem['image'])) ?>" alt="Preview" style="max-height: 120px; border-radius: 6px; border: 1px solid #ddd;">
            </div>
          <?php endif; ?>
          <input type="file" id="image" name="image" class="admin-input" accept="image/*">
        </div>

        <div style="display: flex; gap: 24px; align-items: center; margin: 24px 0;">
          <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <input type="checkbox" name="is_portfolio" value="1" <?= (!isset($editItem) || $editItem['is_portfolio']) ? 'checked' : '' ?>>
            <span>Display on Home Page Representative Portfolio</span>
          </label>
        </div>

        <button type="submit" class="btn-admin btn-admin--primary" style="padding: 10px 24px;">
          <?= $action === 'create' ? 'Save and Publish' : 'Update Property' ?>
        </button>
      </form>
    </div>
  </div>

<?php else: ?>
  <!-- Properties Table -->
  <div class="admin-card">
    <div class="admin-card__header">
      <h2 class="admin-card__title">Properties (<?= e((string)$list['total']) ?>)</h2>
      <a href="<?= e(url('admin/properties.php?action=create')) ?>" class="btn-admin btn-admin--primary btn-admin--sm">
        + Add New
      </a>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th>Preview</th>
          <th>Reference</th>
          <th>Title</th>
          <th>Location</th>
          <th>Type</th>
          <th>Status</th>
          <th>Portfolio</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($list['data'])): ?>
          <tr><td colspan="8" style="text-align: center; color: #888;">No properties listed.</td></tr>
        <?php else: ?>
          <?php foreach ($list['data'] as $prop): ?>
            <tr>
              <td style="width: 70px;">
                <img src="<?= e(url($prop['image'])) ?>" alt="<?= e($prop['title']) ?>" style="width: 60px; height: 42px; object-fit: cover; border-radius: 4px;">
              </td>
              <td><strong><?= e($prop['reference']) ?></strong></td>
              <td><?= e($prop['title']) ?></td>
              <td><?= e($prop['location']) ?></td>
              <td><?= e($prop['type']) ?></td>
              <td><span class="badge badge--sold"><?= e($prop['status']) ?></span></td>
              <td><?= $prop['is_portfolio'] ? '✅ Yes' : '—' ?></td>
              <td>
                <div style="display: flex; gap: 6px;">
                  <a href="<?= e(url('admin/properties.php?action=edit&id=' . $prop['id'])) ?>" class="btn-admin btn-admin--sm btn-admin--secondary">Edit</a>
                  <form method="post" action="<?= e(url('admin/properties.php?action=delete&id=' . $prop['id'])) ?>" onsubmit="return confirm('Delete this property listing?');" style="display: inline;">
                    <button type="submit" class="btn-admin btn-admin--sm btn-admin--danger">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
