<?php
$pageTitle = 'Messages';
require_once 'includes/header.php';
if ($db && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try {
        $action = $_POST['form_action'] ?? '';
        $id = (int) ($_POST['id'] ?? 0);
        if ($action === 'delete') {
            $stmt = $db->prepare('DELETE FROM contact_messages WHERE id=?');
            $stmt->execute([$id]);
            admin_redirect('messages.php', 'Message deleted.');
        }
        if ($action === 'status') {
            $status = $_POST['status'] === 'read' ? 'read' : 'unread';
            $stmt = $db->prepare('UPDATE contact_messages SET status=? WHERE id=?');
            $stmt->execute([$status, $id]);
            admin_redirect('messages.php', 'Message marked ' . $status . '.');
        }
    } catch (Throwable $exception) {
        $formError = $exception->getMessage();
    }
}
$messages = $db ? $db->query('SELECT * FROM contact_messages ORDER BY created_at DESC, id DESC')->fetchAll() : [];
?>
<div class="admin-heading">
    <div>
        <h1>Messages</h1>
        <p>Review enquiries sent through the portfolio.</p>
    </div>
</div>
<?php if (!empty($formError)): ?>
    <div class="admin-alert admin-alert--error"><?= admin_escape($formError) ?></div><?php endif; ?>
<section class="admin-card">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Received</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody><?php foreach ($messages as $message): ?>
                    <tr>
                        <td>
                            <div class="admin-table__title"><?= admin_escape($message['name']) ?></div><a class="text-meta"
                                href="mailto:<?= admin_escape($message['email']) ?>"><?= admin_escape($message['email']) ?></a>
                        </td>
                        <td><?= admin_escape($message['subject'] ?: 'No subject') ?></td>
                        <td><?= admin_escape(mb_strimwidth($message['message'], 0, 90, '...')) ?></td>
                        <td><?= admin_escape(date('M j, Y', strtotime($message['created_at']))) ?></td>
                        <td><span
                                class="admin-badge admin-badge--<?= admin_escape($message['status']) ?>"><?= admin_escape($message['status']) ?></span>
                        </td>
                        <td>
                            <div class="admin-row-actions">
                                <form method="post"><?= admin_csrf_field() ?><input type="hidden" name="form_action"
                                        value="status"><input type="hidden" name="id"
                                        value="<?= (int) $message['id'] ?>"><input type="hidden" name="status"
                                        value="<?= $message['status'] === 'unread' ? 'read' : 'unread' ?>"><button
                                        class="admin-icon-button" type="submit"
                                        aria-label="Mark message <?= $message['status'] === 'unread' ? 'read' : 'unread' ?>"><i
                                            data-lucide="mail-<?= $message['status'] === 'unread' ? 'open' : 'plus' ?>"
                                            aria-hidden="true"></i></button></form>
                                <form method="post" onsubmit="return confirm('Delete this message?');">
                                    <?= admin_csrf_field() ?><input type="hidden" name="form_action" value="delete"><input
                                        type="hidden" name="id" value="<?= (int) $message['id'] ?>"><button
                                        class="admin-icon-button" type="submit" aria-label="Delete message"><i
                                            data-lucide="trash-2" aria-hidden="true"></i></button></form>
                            </div>
                        </td>
                    </tr><?php endforeach; ?><?php if (!$messages): ?>
                    <tr>
                        <td colspan="6" class="admin-empty">No messages yet.</td>
                    </tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require_once 'includes/footer.php';
