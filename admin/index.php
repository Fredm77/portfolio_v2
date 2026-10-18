<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';

$stats = ['projects' => 0, 'skills' => 0, 'experience' => 0, 'messages' => 0];
$recentProjects = [];
$recentMessages = [];
if ($db) {
    $stats['projects'] = (int) $db->query('SELECT COUNT(*) FROM projects')->fetchColumn();
    $stats['skills'] = (int) $db->query('SELECT COUNT(*) FROM skills')->fetchColumn();
    $stats['experience'] = (int) $db->query('SELECT COUNT(*) FROM experiences')->fetchColumn();
    $stats['messages'] = (int) $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();
    $recentProjects = $db->query('SELECT id, title, status, created_at FROM projects ORDER BY created_at DESC LIMIT 5')->fetchAll();
    $recentMessages = $db->query('SELECT id, name, subject, status, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
}
?>
<div class="admin-heading">
    <div>
        <h1>Dashboard</h1>
        <p>A current view of your portfolio content.</p>
    </div>
    <div class="admin-actions"><a href="projects.php?action=add" class="btn btn-primary"><i data-lucide="plus"
                aria-hidden="true"></i> Add project</a></div>
</div>

<div class="admin-grid admin-grid--stats">
    <div class="admin-card"><span class="admin-stat__label">Total Projects</span><strong
            class="admin-stat__value"><?= $stats['projects'] ?></strong></div>
    <div class="admin-card"><span class="admin-stat__label">Total Skills</span><strong
            class="admin-stat__value"><?= $stats['skills'] ?></strong></div>
    <div class="admin-card"><span class="admin-stat__label">Experience Entries</span><strong
            class="admin-stat__value"><?= $stats['experience'] ?></strong></div>
    <div class="admin-card"><span class="admin-stat__label">Unread Messages</span><strong
            class="admin-stat__value <?= $stats['messages'] ? 'is-accent' : '' ?>"><?= $stats['messages'] ?></strong>
    </div>
</div>

<div class="admin-grid admin-grid--two">
    <section class="admin-card">
        <div class="admin-card__header">
            <h2>Recent Projects</h2><a href="projects.php" class="text-small link-underline">View all</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentProjects as $project): ?>
                        <tr>
                            <td class="admin-table__title"><?= admin_escape($project['title']) ?></td>
                            <td><span
                                    class="admin-badge admin-badge--<?= admin_escape($project['status']) ?>"><?= admin_escape($project['status']) ?></span>
                            </td>
                            <td><?= admin_escape(date('M j, Y', strtotime($project['created_at']))) ?></td>
                        </tr><?php endforeach; ?>
                    <?php if (!$recentProjects): ?>
                        <tr>
                            <td colspan="3" class="admin-empty">No projects yet.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <section class="admin-card">
        <div class="admin-card__header">
            <h2>Recent Messages</h2><a href="messages.php" class="text-small link-underline">View all</a>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentMessages as $message): ?>
                        <tr>
                            <td class="admin-table__title"><?= admin_escape($message['name']) ?></td>
                            <td><?= admin_escape($message['subject'] ?: 'No subject') ?></td>
                            <td><span
                                    class="admin-badge admin-badge--<?= admin_escape($message['status']) ?>"><?= admin_escape($message['status']) ?></span>
                            </td>
                        </tr><?php endforeach; ?>
                    <?php if (!$recentMessages): ?>
                        <tr>
                            <td colspan="3" class="admin-empty">No messages yet.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
<?php require_once 'includes/footer.php'; ?>