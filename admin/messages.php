<?php
/**
 * HS Properties — Client Contact Inquiries.
 */

declare(strict_types=1);

$activePage = 'messages';
$adminTitle = 'Client Messages';

require_once __DIR__ . '/../backend/bootstrap.php';

$db = Database::getInstance();

// Mark as read/archive
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $msgId = (int)$_POST['id'];
    $status = $_POST['status'];
    $db->update('contact_messages', ['status' => $status], '`id` = :id', ['id' => $msgId]);
    Session::flash('success', 'Message marked as ' . $status);
    redirect(url('admin/messages.php'));
}

$messages = $db->fetchAll('SELECT * FROM `contact_messages` ORDER BY `created_at` DESC');

include __DIR__ . '/header.php';
?>

<div class="admin-card">
  <div class="admin-card__header">
    <h2 class="admin-card__title">Direct Contact Inquiries (<?= count($messages) ?>)</h2>
  </div>

  <table class="admin-table">
    <thead>
      <tr>
        <th>From</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Subject</th>
        <th>Message</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($messages)): ?>
        <tr><td colspan="8" style="text-align: center; color: #888;">No contact inquiries received.</td></tr>
      <?php else: ?>
        <?php foreach ($messages as $m): ?>
          <tr>
            <td><strong><?= e($m['full_name']) ?></strong></td>
            <td><a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a></td>
            <td><?= e($m['phone'] ?: '—') ?></td>
            <td><?= e($m['subject'] ?: 'Inquiry') ?></td>
            <td style="max-width: 300px;"><?= nl2br(e($m['message'])) ?></td>
            <td><span class="badge badge--<?= $m['status'] === 'unread' ? 'new' : 'draft' ?>"><?= e($m['status']) ?></span></td>
            <td><?= e(date('d M Y', strtotime($m['created_at']))) ?></td>
            <td>
              <form method="post" action="<?= e(url('admin/messages.php')) ?>" style="display: inline;">
                <input type="hidden" name="id" value="<?= e((string)$m['id']) ?>">
                <?php if ($m['status'] === 'unread'): ?>
                  <input type="hidden" name="action" value="mark_read">
                  <input type="hidden" name="status" value="read">
                  <button type="submit" class="btn-admin btn-admin--sm btn-admin--secondary">Mark Read</button>
                <?php else: ?>
                  <input type="hidden" name="action" value="archive">
                  <input type="hidden" name="status" value="archived">
                  <button type="submit" class="btn-admin btn-admin--sm btn-admin--secondary">Archive</button>
                <?php endif; ?>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
