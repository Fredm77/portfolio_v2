<?php
// admin_scaffold.php
$dir = __DIR__ . '/admin';
@mkdir($dir . '/includes', 0777, true);

// 1. HEADER
$header = <<<'EOT'
<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/database.php';
$db = (new Database())->getConnection();

// Get unread messages count
$unreadCount = 0;
if ($db) {
    $unreadCount = $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background: var(--bg-base); display: flex; min-height: 100vh; margin: 0; }
        .admin-sidebar { width: 260px; background: var(--bg-surface); border-right: 1px solid var(--line); padding: var(--space-6); display: flex; flex-direction: column; flex-shrink: 0; }
        .admin-main { flex-grow: 1; padding: var(--space-8); overflow-y: auto; }
        .admin-nav { display: flex; flex-direction: column; gap: var(--space-2); margin-top: var(--space-8); flex-grow: 1; }
        .admin-nav a { display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3) var(--space-4); border-radius: var(--radius-md); color: var(--text-secondary); text-decoration: none; font-weight: 500; transition: all var(--dur-fast); }
        .admin-nav a:hover, .admin-nav a.active { background: var(--bg-elevated); color: var(--accent); }
        .admin-nav a .badge { margin-left: auto; background: var(--accent); color: var(--text-on-accent); border-radius: var(--radius-sm); padding: 2px 8px; font-size: 12px; }
        .admin-card { background: var(--bg-surface); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: var(--space-6); }
        .admin-table { width: 100%; border-collapse: collapse; text-align: left; }
        .admin-table th, .admin-table td { padding: var(--space-3); border-bottom: 1px solid var(--line); }
        .admin-table th { color: var(--text-muted); font-size: var(--type-small); font-weight: 500; }
        .admin-table tr:hover td { background: var(--bg-elevated); }
        .btn-sm { padding: 6px 12px; font-size: var(--type-small); }
        .flex-between { display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-6); }
        .stat-card { background: var(--bg-surface); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: var(--space-6); display: flex; flex-direction: column; }
        .stat-card-title { color: var(--text-muted); font-size: var(--type-small); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat-card-value { font-family: var(--font-heading); font-size: var(--type-h2); font-weight: 700; color: var(--text-primary); margin-top: var(--space-2); }
    </style>
</head>
<body>
    <aside class="admin-sidebar">
        <h3 style="font-family: var(--font-heading); color: var(--text-primary); margin: 0;">FRED<span style="color: var(--accent);">.</span> Admin</h3>
        <nav class="admin-nav">
            <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>"><i data-lucide="layout-dashboard" style="width: 18px;"></i> Dashboard</a>
            <a href="projects.php" class="<?= strpos($current_page, 'project') !== false ? 'active' : '' ?>"><i data-lucide="folder" style="width: 18px;"></i> Projects</a>
            <a href="skills.php" class="<?= $current_page == 'skills.php' ? 'active' : '' ?>"><i data-lucide="cpu" style="width: 18px;"></i> Skills</a>
            <a href="experience.php" class="<?= $current_page == 'experience.php' ? 'active' : '' ?>"><i data-lucide="briefcase" style="width: 18px;"></i> Experience</a>
            <a href="education.php" class="<?= $current_page == 'education.php' ? 'active' : '' ?>"><i data-lucide="graduation-cap" style="width: 18px;"></i> Education</a>
            <a href="messages.php" class="<?= $current_page == 'messages.php' ? 'active' : '' ?>"><i data-lucide="mail" style="width: 18px;"></i> Messages <?= $unreadCount > 0 ? "<span class='badge'>$unreadCount</span>" : '' ?></a>
            <a href="socials.php" class="<?= $current_page == 'socials.php' ? 'active' : '' ?>"><i data-lucide="link" style="width: 18px;"></i> Social Links</a>
            <a href="settings.php" class="<?= $current_page == 'settings.php' ? 'active' : '' ?>"><i data-lucide="settings" style="width: 18px;"></i> Settings</a>
        </nav>
        <div style="margin-top: auto; padding-top: var(--space-6); border-top: 1px solid var(--line);">
            <div class="text-small text-muted" style="margin-bottom: var(--space-3);">Logged in as <strong><?= htmlspecialchars($_SESSION['admin_name']) ?></strong></div>
            <a href="logout.php" class="btn btn-ghost" style="width: 100%; justify-content: center; color: var(--danger);"><i data-lucide="log-out" style="width: 16px;"></i> Logout</a>
        </div>
    </aside>
    <main class="admin-main">
EOT;
file_put_contents($dir . '/includes/header.php', $header);

// 2. FOOTER
$footer = <<<'EOT'
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>
EOT;
file_put_contents($dir . '/includes/footer.php', $footer);

// 3. INDEX (Dashboard)
$index = <<<'EOT'
<?php
require_once 'includes/header.php';

$stats = [
    'projects' => $db->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'skills' => $db->query("SELECT COUNT(*) FROM skills")->fetchColumn(),
    'experience' => $db->query("SELECT COUNT(*) FROM experiences")->fetchColumn(),
    'messages' => $unreadCount
];

$recent_projects = $db->query("SELECT id, title, status, created_at FROM projects ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_messages = $db->query("SELECT id, name, subject, created_at, status FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<h1 class="display" style="font-size: var(--type-h2); margin-bottom: var(--space-6);">Dashboard</h1>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-6); margin-bottom: var(--space-8);">
    <div class="stat-card">
        <span class="stat-card-title">Total Projects</span>
        <span class="stat-card-value"><?= $stats['projects'] ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-card-title">Total Skills</span>
        <span class="stat-card-value"><?= $stats['skills'] ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-card-title">Experience Entries</span>
        <span class="stat-card-value"><?= $stats['experience'] ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-card-title">Unread Messages</span>
        <span class="stat-card-value" style="color: <?= $stats['messages'] > 0 ? 'var(--accent)' : 'inherit' ?>;"><?= $stats['messages'] ?></span>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6);">
    <div class="admin-card">
        <div class="flex-between">
            <h3 style="margin: 0; font-family: var(--font-heading);">Recent Projects</h3>
            <a href="projects.php" class="text-small link-underline">View all</a>
        </div>
        <table class="admin-table">
            <tr><th>Title</th><th>Status</th><th>Date</th></tr>
            <?php foreach($recent_projects as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['title']) ?></td>
                <td><span class="badge" style="background: <?= $p['status'] == 'published' ? 'var(--support-bg)' : 'var(--bg-inset)' ?>; color: <?= $p['status'] == 'published' ? 'var(--support)' : 'var(--text-muted)' ?>; border: 1px solid currentColor;"><?= $p['status'] ?></span></td>
                <td class="text-muted text-small"><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($recent_projects)): ?><tr><td colspan="3" class="text-muted text-center">No projects yet.</td></tr><?php endif; ?>
        </table>
    </div>

    <div class="admin-card">
        <div class="flex-between">
            <h3 style="margin: 0; font-family: var(--font-heading);">Recent Messages</h3>
            <a href="messages.php" class="text-small link-underline">View all</a>
        </div>
        <table class="admin-table">
            <tr><th>From</th><th>Subject</th><th>Status</th></tr>
            <?php foreach($recent_messages as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['name']) ?></td>
                <td><?= htmlspecialchars($m['subject']) ?></td>
                <td>
                    <?php if($m['status'] == 'unread'): ?>
                        <span style="color: var(--accent); font-size: var(--type-small); font-weight: 500;">Unread</span>
                    <?php else: ?>
                        <span class="text-muted text-small">Read</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($recent_messages)): ?><tr><td colspan="3" class="text-muted text-center">No messages yet.</td></tr><?php endif; ?>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
EOT;
file_put_contents($dir . '/index.php', $index);

echo "Base admin scaffold generated.\n";

