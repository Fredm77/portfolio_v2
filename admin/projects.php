<?php
$pageTitle = 'Projects';
require_once 'includes/header.php';

if (!$db) {
    echo '<div class="admin-alert admin-alert--error">Database connection is unavailable.</div>';
    require_once 'includes/footer.php';
    exit;
}

$extendedColumns = ['technologies' => 'VARCHAR(255)', 'role' => 'VARCHAR(100)', 'problem' => 'TEXT', 'solution' => 'TEXT', 'key_features' => 'TEXT', 'challenges' => 'TEXT', 'results' => 'TEXT', 'year' => 'VARCHAR(20)'];
$existingColumns = [];
foreach ($db->query('SHOW COLUMNS FROM projects')->fetchAll() as $column) {
    $existingColumns[$column['Field']] = true;
}
foreach ($extendedColumns as $column => $type) {
    if (!isset($existingColumns[$column])) {
        try {
            $db->exec("ALTER TABLE projects ADD COLUMN $column $type");
            $existingColumns[$column] = true;
        } catch (PDOException $exception) {
        }
    }
}

$action = $_GET['action'] ?? 'list';
$projectId = (int) ($_GET['id'] ?? 0);
$project = null;
$gallery = [];
if ($projectId) {
    $statement = $db->prepare('SELECT * FROM projects WHERE id = ?');
    $statement->execute([$projectId]);
    $project = $statement->fetch();
    if ($project) {
        $galleryStatement = $db->prepare('SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC, id ASC');
        $galleryStatement->execute([$projectId]);
        $gallery = $galleryStatement->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $formAction = $_POST['form_action'] ?? '';
    try {
        if ($formAction === 'delete') {
            $statement = $db->prepare('DELETE FROM projects WHERE id = ?');
            $statement->execute([(int) $_POST['id']]);
            admin_redirect('projects.php', 'Project deleted.');
        }
        if ($formAction === 'delete_image') {
            $statement = $db->prepare('DELETE FROM project_images WHERE id = ? AND project_id = ?');
            $statement->execute([(int) $_POST['image_id'], (int) $_POST['project_id']]);
            admin_redirect('projects.php?action=edit&id=' . (int) $_POST['project_id'], 'Screenshot removed.');
        }
        if ($formAction === 'save_image') {
            $imagePath = admin_upload($_FILES['screenshot'] ?? [], 'projects', ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif']);
            if ($imagePath === '') {
                throw new RuntimeException('Choose a screenshot to upload.');
            }
            $statement = $db->prepare('INSERT INTO project_images (project_id, image, caption, sort_order) VALUES (?, ?, ?, ?)');
            $statement->execute([(int) $_POST['project_id'], $imagePath, trim($_POST['caption'] ?? ''), (int) ($_POST['sort_order'] ?? 0)]);
            admin_redirect('projects.php?action=edit&id=' . (int) $_POST['project_id'], 'Screenshot uploaded.');
        }
        if ($formAction === 'save_project') {
            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            if ($title === '' || $slug === '') {
                throw new RuntimeException('Title and slug are required.');
            }
            if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
                throw new RuntimeException('Use lowercase letters, numbers, and hyphens for the slug.');
            }
            $image = trim($_POST['current_image'] ?? '');
            if (!empty($_FILES['image']['name'])) {
                $image = admin_upload($_FILES['image'], 'projects', ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif']);
            }
            $values = [
                $title,
                $slug,
                trim($_POST['short_description'] ?? ''),
                trim($_POST['description'] ?? ''),
                trim($_POST['category'] ?? ''),
                $image,
                trim($_POST['technologies'] ?? ''),
                trim($_POST['role'] ?? ''),
                trim($_POST['problem'] ?? ''),
                trim($_POST['solution'] ?? ''),
                trim($_POST['key_features'] ?? ''),
                trim($_POST['challenges'] ?? ''),
                trim($_POST['results'] ?? ''),
                trim($_POST['year'] ?? ''),
                trim($_POST['github_url'] ?? ''),
                trim($_POST['live_url'] ?? ''),
                !empty($_POST['featured']) ? 1 : 0,
                $_POST['status'] ?? 'draft',
                (int) ($_POST['sort_order'] ?? 0)
            ];
            if ($projectId && $project) {
                $values[] = $projectId;
                $statement = $db->prepare('UPDATE projects SET title=?, slug=?, short_description=?, description=?, category=?, image=?, technologies=?, role=?, problem=?, solution=?, key_features=?, challenges=?, results=?, year=?, github_url=?, live_url=?, featured=?, status=?, sort_order=? WHERE id=?');
                $statement->execute($values);
                admin_redirect('projects.php?action=edit&id=' . $projectId, 'Project updated.');
            }
            $statement = $db->prepare('INSERT INTO projects (title, slug, short_description, description, category, image, technologies, role, problem, solution, key_features, challenges, results, year, github_url, live_url, featured, status, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $statement->execute($values);
            admin_redirect('projects.php', 'Project created.');
        }
    } catch (Throwable $exception) {
        $formError = $exception->getMessage();
    }
}

if ($action === 'list') {
    $projects = $db->query('SELECT id, title, slug, category, featured, status, image, updated_at FROM projects ORDER BY featured DESC, sort_order ASC, created_at DESC')->fetchAll();
    ?>
    <div class="admin-heading">
        <div>
            <h1>Projects</h1>
            <p>Manage the work shown on your portfolio.</p>
        </div><a href="projects.php?action=add" class="btn btn-primary"><i data-lucide="plus" aria-hidden="true"></i> Add
            project</a>
    </div>
    <section class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $item): ?>
                        <tr>
                            <td>
                                <div class="admin-table__title"><?= admin_escape($item['title']) ?></div><span
                                    class="text-meta text-muted"><?= admin_escape($item['slug']) ?></span>
                            </td>
                            <td><?= admin_escape($item['category']) ?></td>
                            <td><span
                                    class="admin-badge admin-badge--<?= admin_escape($item['status']) ?>"><?= admin_escape($item['status']) ?></span>
                            </td>
                            <td><?= $item['featured'] ? '<span class="admin-badge admin-badge--draft">Yes</span>' : '<span class="text-muted">No</span>' ?>
                            </td>
                            <td>
                                <div class="admin-row-actions"><a class="admin-icon-button"
                                        href="projects.php?action=edit&id=<?= (int) $item['id'] ?>"
                                        aria-label="Edit <?= admin_escape($item['title']) ?>"><i data-lucide="pencil"
                                            aria-hidden="true"></i></a>
                                    <form method="post" onsubmit="return confirm('Delete this project and its screenshots?');">
                                        <?= admin_csrf_field() ?><input type="hidden" name="form_action" value="delete"><input
                                            type="hidden" name="id" value="<?= (int) $item['id'] ?>"><button
                                            class="admin-icon-button" type="submit"
                                            aria-label="Delete <?= admin_escape($item['title']) ?>"><i data-lucide="trash-2"
                                                aria-hidden="true"></i></button></form>
                                </div>
                            </td>
                        </tr><?php endforeach; ?>
                    <?php if (!$projects): ?>
                        <tr>
                            <td colspan="5" class="admin-empty">No projects yet. Add the first project to begin.</td>
                        </tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php
} elseif ($action === 'edit' || $action === 'add') {
    $isEdit = $action === 'edit' && $project;
    if (!$isEdit) {
        $project = array_fill_keys(array_keys($extendedColumns), '');
        $project = array_merge(['title' => '', 'slug' => '', 'short_description' => '', 'description' => '', 'category' => '', 'image' => '', 'github_url' => '', 'live_url' => '', 'featured' => 0, 'status' => 'draft', 'sort_order' => 0], $project);
    }
    ?>
    <div class="admin-heading">
        <div>
            <h1><?= $isEdit ? 'Edit project' : 'Add project' ?></h1>
            <p><?= $isEdit ? 'Update project details and screenshots.' : 'Add a project to your portfolio.' ?></p>
        </div><a href="projects.php" class="btn btn-secondary"><i data-lucide="arrow-left" aria-hidden="true"></i> Back to
            projects</a>
    </div>
    <?php if (!empty($formError)): ?>
        <div class="admin-alert admin-alert--error" role="alert"><?= admin_escape($formError) ?></div><?php endif; ?>
    <form class="admin-card admin-form" method="post" enctype="multipart/form-data">
        <?= admin_csrf_field() ?><input type="hidden" name="form_action" value="save_project"><input type="hidden"
            name="current_image" value="<?= admin_escape($project['image']) ?>">
        <div class="admin-form__grid">
            <div><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title"
                    required value="<?= admin_escape($project['title']) ?>"></div>
            <div><label class="form-label" for="slug">Slug</label><input class="form-control" id="slug" name="slug" required
                    pattern="[a-z0-9]+(?:-[a-z0-9]+)*" value="<?= admin_escape($project['slug']) ?>"><small
                    class="form-help">Example: daily-reporting-system</small></div>
            <div class="admin-form__full"><label class="form-label" for="short_description">Short description</label><input
                    class="form-control" id="short_description" name="short_description" maxlength="255"
                    value="<?= admin_escape($project['short_description']) ?>"></div>
            <div><label class="form-label" for="category">Category</label><input class="form-control" id="category"
                    name="category" value="<?= admin_escape($project['category']) ?>"></div>
            <div><label class="form-label" for="year">Year</label><input class="form-control" id="year" name="year"
                    value="<?= admin_escape($project['year'] ?? '') ?>"></div>
            <div class="admin-form__full"><label class="form-label" for="description">Description</label><textarea
                    class="form-control" id="description"
                    name="description"><?= admin_escape($project['description']) ?></textarea></div>
            <div class="admin-form__full"><label class="form-label" for="technologies">Technologies</label><input
                    class="form-control" id="technologies" name="technologies"
                    value="<?= admin_escape($project['technologies'] ?? '') ?>"><small class="form-help">Comma-separated,
                    for example PHP, MySQL, Bootstrap.</small></div>
            <div><label class="form-label" for="role">Role</label><input class="form-control" id="role" name="role"
                    value="<?= admin_escape($project['role'] ?? '') ?>"></div>
            <div><label class="form-label" for="sort_order">Sort order</label><input class="form-control" id="sort_order"
                    name="sort_order" type="number" value="<?= (int) $project['sort_order'] ?>"></div>
            <?php foreach (['problem' => 'The problem', 'solution' => 'The solution', 'key_features' => 'Key features', 'challenges' => 'Challenges', 'results' => 'Results'] as $field => $label): ?>
                <div class="admin-form__full"><label class="form-label" for="<?= $field ?>"><?= $label ?></label><textarea
                        class="form-control" id="<?= $field ?>"
                        name="<?= $field ?>"><?= admin_escape($project[$field] ?? '') ?></textarea></div><?php endforeach; ?>
            <div><label class="form-label" for="github_url">GitHub URL</label><input class="form-control" id="github_url"
                    name="github_url" type="url" value="<?= admin_escape($project['github_url']) ?>"></div>
            <div><label class="form-label" for="live_url">Live URL</label><input class="form-control" id="live_url"
                    name="live_url" type="url" value="<?= admin_escape($project['live_url']) ?>"></div>
            <div><label class="form-label" for="status">Status</label><select class="form-select" id="status" name="status">
                    <option value="draft" <?= $project['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="published" <?= $project['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="archived" <?= $project['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                </select></div>
            <label class="form-check"><input type="checkbox" name="featured" value="1" <?= !empty($project['featured']) ? 'checked' : '' ?>> Featured project</label>
            <div class="admin-form__full"><label class="form-label" for="image">Primary image</label><input
                    class="form-control" id="image" name="image" type="file"
                    accept="image/jpeg,image/png,image/webp,image/gif"><small class="form-help">JPG, PNG, WebP, or GIF, up
                    to 8MB.</small><?php if (!empty($project['image'])): ?><img class="admin-thumb"
                        style="margin-top:10px;width:120px;height:80px" src="../<?= admin_escape($project['image']) ?>"
                        alt="Current project image"><?php endif; ?></div>
        </div>
        <div class="admin-form__actions"><a href="projects.php" class="btn btn-secondary">Cancel</a><button
                class="btn btn-primary" type="submit">Save project</button></div>
    </form>
    <?php if ($isEdit): ?>
        <section class="admin-card" style="margin-top: var(--space-6)">
            <div class="admin-card__header">
                <h2>Screenshots</h2>
            </div>
            <div class="admin-upload-list"><?php foreach ($gallery as $image): ?>
                    <div class="admin-upload-item"><img src="../<?= admin_escape($image['image']) ?>"
                            alt="<?= admin_escape($image['caption']) ?>">
                        <form method="post" onsubmit="return confirm('Remove this screenshot?');"><?= admin_csrf_field() ?><input
                                type="hidden" name="form_action" value="delete_image"><input type="hidden" name="image_id"
                                value="<?= (int) $image['id'] ?>"><input type="hidden" name="project_id"
                                value="<?= $projectId ?>"><span
                                class="text-meta text-muted"><?= admin_escape($image['caption']) ?></span><button
                                class="admin-icon-button" type="submit" aria-label="Delete screenshot"><i data-lucide="trash-2"
                                    aria-hidden="true"></i></button></form>
                    </div><?php endforeach; ?><?php if (!$gallery): ?>
                    <p class="admin-empty">No screenshots uploaded.</p><?php endif; ?>
            </div>
            <form class="admin-form" style="margin-top: var(--space-6)" method="post" enctype="multipart/form-data">
                <?= admin_csrf_field() ?><input type="hidden" name="form_action" value="save_image"><input type="hidden"
                    name="project_id" value="<?= $projectId ?>">
                <div class="admin-form__grid">
                    <div><label class="form-label" for="screenshot">Screenshot</label><input class="form-control"
                            id="screenshot" name="screenshot" type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                            required></div>
                    <div><label class="form-label" for="caption">Caption</label><input class="form-control" id="caption"
                            name="caption"></div>
                    <div><label class="form-label" for="image_sort_order">Sort order</label><input class="form-control"
                            id="image_sort_order" name="sort_order" type="number" value="0"></div>
                </div>
                <div class="admin-form__actions"><button class="btn btn-secondary" type="submit">Upload screenshot</button>
                </div>
            </form>
        </section>
    <?php endif; ?>
<?php
}
require_once 'includes/footer.php';
