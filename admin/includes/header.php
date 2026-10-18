<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../../config/database.php';

$db = (new Database())->getConnection();
$currentPage = basename($_SERVER['PHP_SELF']);
$unreadCount = 0;
if ($db) {
    $unreadCount = (int) $db->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'")->fetchColumn();
}

function admin_escape($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function admin_csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . admin_escape($_SESSION['csrf_token']) . '">';
}

function admin_redirect($location, $message = '', $type = 'success') {
    if ($message !== '') {
        $_SESSION['admin_flash'] = ['message' => $message, 'type' => $type];
    }
    header('Location: ' . $location);
    exit;
}

function admin_flash() {
    if (empty($_SESSION['admin_flash'])) {
        return;
    }
    $flash = $_SESSION['admin_flash'];
    unset($_SESSION['admin_flash']);
    echo '<div class="admin-alert admin-alert--' . admin_escape($flash['type']) . '" role="status">' . admin_escape($flash['message']) . '</div>';
}

function admin_upload($file, $directory, $allowedMimeTypes) {
    if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 8 * 1024 * 1024) {
        throw new RuntimeException('The uploaded file is invalid or larger than 8MB.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!isset($allowedMimeTypes[$mime])) {
        throw new RuntimeException('This file type is not supported.');
    }
    $targetDirectory = __DIR__ . '/../../uploads/' . trim($directory, '/');
    if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0755, true)) {
        throw new RuntimeException('The upload directory could not be created.');
    }
    $filename = bin2hex(random_bytes(12)) . '.' . $allowedMimeTypes[$mime];
    if (!move_uploaded_file($file['tmp_name'], $targetDirectory . '/' . $filename)) {
        throw new RuntimeException('The file could not be saved.');
    }
    return 'uploads/' . trim($directory, '/') . '/' . $filename;
}

$navItems = [
    ['page' => 'index.php', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
    ['page' => 'projects.php', 'label' => 'Projects', 'icon' => 'folder-kanban'],
    ['page' => 'skills.php', 'label' => 'Skills', 'icon' => 'cpu'],
    ['page' => 'experience.php', 'label' => 'Experience', 'icon' => 'briefcase-business'],
    ['page' => 'education.php', 'label' => 'Education', 'icon' => 'graduation-cap'],
    ['page' => 'messages.php', 'label' => 'Messages', 'icon' => 'mail'],
    ['page' => 'socials.php', 'label' => 'Social Links', 'icon' => 'link'],
    ['page' => 'settings.php', 'label' => 'Settings', 'icon' => 'settings'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= admin_escape($pageTitle ?? 'Admin') ?> | MUKIZA FRED</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="admin-body">
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="admin-brand">FRED<span>.</span><small>Admin</small></div>
        <nav class="admin-nav" aria-label="Admin navigation">
            <?php foreach ($navItems as $item): ?>
                <a href="<?= admin_escape($item['page']) ?>" class="admin-nav__link <?= $currentPage === $item['page'] ? 'is-active' : '' ?>">
                    <i data-lucide="<?= admin_escape($item['icon']) ?>" aria-hidden="true"></i>
                    <span><?= admin_escape($item['label']) ?></span>
                    <?php if ($item['page'] === 'messages.php' && $unreadCount > 0): ?><b class="admin-nav__count"><?= $unreadCount ?></b><?php endif; ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="admin-sidebar__footer">
            <span class="admin-user">Signed in as <?= admin_escape($_SESSION['admin_name'] ?? 'Administrator') ?></span>
            <a href="logout.php" class="admin-logout"><i data-lucide="log-out" aria-hidden="true"></i> Log out</a>
        </div>
    </aside>
    <div class="admin-page">
        <header class="admin-topbar">
            <button class="admin-menu-toggle" type="button" aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open navigation"><i data-lucide="menu" aria-hidden="true"></i></button>
            <span class="admin-topbar__section"><?= admin_escape($pageTitle ?? 'Admin') ?></span>
            <a href="../index.php" target="_blank" rel="noopener" class="admin-view-site">View site <i data-lucide="arrow-up-right" aria-hidden="true"></i></a>
        </header>
        <main class="admin-main">
            <?php admin_flash(); ?>
