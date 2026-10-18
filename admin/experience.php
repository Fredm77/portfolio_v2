<?php
$pageTitle = 'Experience';
require_once 'includes/header.php';

if ($db && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    try {
        if (($_POST['form_action'] ?? '') === 'delete') {
            $stmt = $db->prepare('DELETE FROM experiences WHERE id = ?');
            $stmt->execute([(int) $_POST['id']]);
            admin_redirect('experience.php', 'Experience entry deleted.');
        }
        if (($_POST['form_action'] ?? '') === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $values = [trim($_POST['organization'] ?? ''), trim($_POST['position'] ?? ''), $_POST['start_date'] ?: null, $_POST['end_date'] ?: null, trim($_POST['description'] ?? ''), trim($_POST['responsibilities'] ?? ''), trim($_POST['technologies'] ?? ''), (int) ($_POST['sort_order'] ?? 0)];
            if ($values[0] === '' || $values[1] === '') {
                throw new RuntimeException('Organization and position are required.');
            }
            if ($id) {
                $values[] = $id;
                $stmt = $db->prepare('UPDATE experiences SET organization=?, position=?, start_date=?, end_date=?, description=?, responsibilities=?, technologies=?, sort_order=? WHERE id=?');
            } else {
                $stmt = $db->prepare('INSERT INTO experiences (organization, position, start_date, end_date, description, responsibilities, technologies, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            }
            $stmt->execute($values);
            admin_redirect('experience.php', 'Experience saved.');
        }
    } catch (Throwable $exception) {
        $formError = $exception->getMessage();
    }
}
$editId = (int) ($_GET['edit'] ?? 0);
$edit = null;
if ($editId && $db) {
    $stmt = $db->prepare('SELECT * FROM experiences WHERE id = ?');
    $stmt->execute([$editId]);
    $edit = $stmt->fetch();
}
$items = $db ? $db->query('SELECT * FROM experiences ORDER BY sort_order ASC, start_date DESC, id ASC')->fetchAll() : [];
?>
<div class="admin-heading">
    <div>
        <h1>Experience</h1>
        <p>Keep your professional timeline in the order it should appear.</p>
    </div><a href="experience.php?action=add" class="btn btn-primary"><i data-lucide="plus" aria-hidden="true"></i> Add
        entry</a>
</div>
<?php if (!empty($formError)): ?>
    <div class="admin-alert admin-alert--error"><?= admin_escape($formError) ?></div><?php endif; ?>
<?php if ($edit || ($_GET['action'] ?? '') === 'add'):
    $edit = $edit ?: ['organization' => '', 'position' => '', 'start_date' => '', 'end_date' => '', 'description' => '', 'responsibilities' => '', 'technologies' => '', 'sort_order' => 0];
    ?>
    <form class="admin-card admin-form" method="post"><?= admin_csrf_field() ?><input type="hidden" name="form_action"
            value="save"><input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
        <div class="admin-form__grid">
            <div><label class="form-label" for="organization">Organization</label><input class="form-control"
                    id="organization" name="organization" required value="<?= admin_escape($edit['organization']) ?>"></div>
            <div><label class="form-label" for="position">Position</label><input class="form-control" id="position"
                    name="position" required value="<?= admin_escape($edit['position']) ?>"></div>
            <div><label class="form-label" for="start_date">Start date</label><input class="form-control" id="start_date"
                    name="start_date" type="date" value="<?= admin_escape($edit['start_date']) ?>"></div>
            <div><label class="form-label" for="end_date">End date</label><input class="form-control" id="end_date"
                    name="end_date" type="date" value="<?= admin_escape($edit['end_date']) ?>"><small
                    class="form-help">Leave blank for current role.</small></div>
            <div><label class="form-label" for="sort_order">Sort order</label><input class="form-control" id="sort_order"
                    name="sort_order" type="number" value="<?= (int) $edit['sort_order'] ?>"></div>
            <div class="admin-form__full"><label class="form-label" for="technologies">Technologies</label><input
                    class="form-control" id="technologies" name="technologies"
                    value="<?= admin_escape($edit['technologies']) ?>"></div>
            <div class="admin-form__full"><label class="form-label" for="description">Description</label><textarea
                    class="form-control" id="description"
                    name="description"><?= admin_escape($edit['description']) ?></textarea></div>
            <div class="admin-form__full"><label class="form-label" for="responsibilities">Responsibilities</label><textarea
                    class="form-control" id="responsibilities"
                    name="responsibilities"><?= admin_escape($edit['responsibilities']) ?></textarea></div>
        </div>
        <div class="admin-form__actions"><a class="btn btn-secondary" href="experience.php">Cancel</a><button
                class="btn btn-primary" type="submit">Save entry</button></div>
    </form>
<?php else: ?>
    <section class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Organization</th>
                        <th>Dates</th>
                        <th>Order</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="admin-table__title"><?= admin_escape($item['position']) ?></td>
                            <td><?= admin_escape($item['organization']) ?></td>
                            <td><?= admin_escape($item['start_date'] ?: '') ?><?= $item['end_date'] ? ' - ' . admin_escape($item['end_date']) : ' - Present' ?>
                            </td>
                            <td><?= (int) $item['sort_order'] ?></td>
                            <td>
                                <div class="admin-row-actions"><a class="admin-icon-button"
                                        href="experience.php?edit=<?= (int) $item['id'] ?>" aria-label="Edit experience"><i
                                            data-lucide="pencil" aria-hidden="true"></i></a>
                                    <form method="post" onsubmit="return confirm('Delete this experience entry?');">
                                        <?= admin_csrf_field() ?><input type="hidden" name="form_action" value="delete"><input
                                            type="hidden" name="id" value="<?= (int) $item['id'] ?>"><button
                                            class="admin-icon-button" type="submit" aria-label="Delete experience"><i
                                                data-lucide="trash-2" aria-hidden="true"></i></button></form>
                                </div>
                            </td>
                        </tr><?php endforeach; ?>
                    <?php if (!$items): ?>
                        <tr>
                            <td colspan="5" class="admin-empty">No experience entries yet.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>
<?php require_once 'includes/footer.php';
