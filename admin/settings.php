<?php
$pageTitle = 'Settings';
require_once 'includes/header.php';
$settingKeys = ['name', 'professional_title', 'short_bio', 'email', 'phone', 'profile_image', 'cv_file', 'youtube_url'];
$settings = [];
if ($db) {
    $rows = $db->query('SELECT setting_key, setting_value FROM site_settings')->fetchAll();
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $db) {
    check_csrf();
    try {
        $settings['profile_image'] = $settings['profile_image'] ?? '';
        $settings['cv_file'] = $settings['cv_file'] ?? '';
        if (!empty($_FILES['profile_image']['name'])) {
            $settings['profile_image'] = admin_upload($_FILES['profile_image'], 'site', ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp']);
        }
        if (!empty($_FILES['cv_file']['name'])) {
            $settings['cv_file'] = admin_upload($_FILES['cv_file'], 'site', ['application/pdf' => 'pdf']);
        }
        $values = ['name', 'professional_title', 'short_bio', 'email', 'phone', 'youtube_url'];
        $stmt = $db->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)');
        foreach ($values as $key) {
            $stmt->execute([$key, trim($_POST[$key] ?? '')]);
        }
        $stmt->execute(['profile_image', $settings['profile_image']]);
        $stmt->execute(['cv_file', $settings['cv_file']]);
        admin_redirect('settings.php', 'Settings updated.');
    } catch (Throwable $exception) {
        $formError = $exception->getMessage();
    }
}
?>
<div class="admin-heading">
    <div>
        <h1>Settings</h1>
        <p>Update the identity and contact details used by the portfolio.</p>
    </div>
</div>
<?php if (!empty($formError)): ?>
    <div class="admin-alert admin-alert--error"><?= admin_escape($formError) ?></div><?php endif; ?>
<form class="admin-card admin-form" method="post" enctype="multipart/form-data"><?= admin_csrf_field() ?>
    <div class="admin-form__grid">
        <div><label class="form-label" for="name">Name</label><input class="form-control" id="name" name="name"
                value="<?= admin_escape($settings['name'] ?? '') ?>" required></div>
        <div><label class="form-label" for="professional_title">Title</label><input class="form-control"
                id="professional_title" name="professional_title"
                value="<?= admin_escape($settings['professional_title'] ?? '') ?>"></div>
        <div class="admin-form__full"><label class="form-label" for="short_bio">Bio</label><textarea
                class="form-control" id="short_bio"
                name="short_bio"><?= admin_escape($settings['short_bio'] ?? '') ?></textarea></div>
        <div><label class="form-label" for="email">Email</label><input class="form-control" id="email" name="email"
                type="email" value="<?= admin_escape($settings['email'] ?? '') ?>"></div>
        <div><label class="form-label" for="phone">Phone</label><input class="form-control" id="phone" name="phone"
                value="<?= admin_escape($settings['phone'] ?? '') ?>"></div>
        <div class="admin-form__full"><label class="form-label" for="youtube_url">YouTube URL</label><input
                class="form-control" id="youtube_url" name="youtube_url" type="url"
                value="<?= admin_escape($settings['youtube_url'] ?? '') ?>"></div>
        <div><label class="form-label" for="profile_image">Profile image</label><input class="form-control"
                id="profile_image" name="profile_image" type="file" accept="image/jpeg,image/png,image/webp"></div>
        <div><label class="form-label" for="cv_file">CV</label><input class="form-control" id="cv_file" name="cv_file"
                type="file" accept="application/pdf"></div>
    </div>
    <div class="admin-form__actions"><button class="btn btn-primary" type="submit">Save settings</button></div>
</form>
<?php require_once 'includes/footer.php';
