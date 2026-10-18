<?php
$pageTitle = 'Social Links';
require_once 'includes/header.php';
if ($db && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try {
        if (($_POST['form_action'] ?? '') === 'save') { $stmt = $db->prepare('UPDATE social_links SET platform=?, url=?, is_visible=?, sort_order=? WHERE id=?'); $stmt->execute([trim($_POST['platform']), trim($_POST['url']), !empty($_POST['is_visible']) ? 1 : 0, (int) $_POST['sort_order'], (int) $_POST['id']]); admin_redirect('socials.php', 'Social link updated.'); }
    } catch (Throwable $exception) { $formError = $exception->getMessage(); }
}
$links = $db ? $db->query('SELECT * FROM social_links ORDER BY sort_order ASC, id ASC')->fetchAll() : [];
?>
<div class="admin-heading"><div><h1>Social Links</h1><p>Control the social links displayed publicly.</p></div></div>
<?php if (!empty($formError)): ?><div class="admin-alert admin-alert--error"><?= admin_escape($formError) ?></div><?php endif; ?>
<section class="admin-card"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Platform</th><th>URL</th><th>Visibility</th><th>Order</th><th></th></tr></thead><tbody><?php foreach ($links as $link): ?><tr><form method="post"><?= admin_csrf_field() ?><input type="hidden" name="form_action" value="save"><input type="hidden" name="id" value="<?= (int) $link['id'] ?>"><td><input class="form-control" name="platform" value="<?= admin_escape($link['platform']) ?>" required></td><td><input class="form-control" name="url" type="url" value="<?= admin_escape($link['url']) ?>" required></td><td><label class="form-check"><input type="checkbox" name="is_visible" value="1" <?= $link['is_visible'] ? 'checked' : '' ?>> Visible</label></td><td><input class="form-control" style="width:80px" name="sort_order" type="number" value="<?= (int) $link['sort_order'] ?>"></td><td><button class="btn btn-secondary btn-sm" type="submit">Save</button></td></form></tr><?php endforeach; ?><?php if (!$links): ?><tr><td colspan="5" class="admin-empty">No social links configured.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require_once 'includes/footer.php';
