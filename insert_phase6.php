<?php
require_once 'config/database.php';
$db = (new Database())->getConnection();

// Update Categories just in case
$db->query("UPDATE skill_categories SET name = 'Programming' WHERE name = 'Languages'");

$categories = [];
$catStmt = $db->query("SELECT id, name FROM skill_categories");
while ($row = $catStmt->fetch()) {
    $categories[$row['name']] = $row['id'];
}

$skills = [
    // Frontend
    ['name' => 'HTML', 'icon' => 'html5', 'cat' => 'Frontend', 'sort' => 1],
    ['name' => 'CSS', 'icon' => 'css3', 'cat' => 'Frontend', 'sort' => 2],
    ['name' => 'JavaScript', 'icon' => 'javascript', 'cat' => 'Frontend', 'sort' => 3],
    ['name' => 'React', 'icon' => 'react', 'cat' => 'Frontend', 'sort' => 4],
    ['name' => 'Bootstrap', 'icon' => 'bootstrap', 'cat' => 'Frontend', 'sort' => 5],
    // Backend
    ['name' => 'Node.js', 'icon' => 'nodedotjs', 'cat' => 'Backend', 'sort' => 1],
    ['name' => 'PHP', 'icon' => 'php', 'cat' => 'Backend', 'sort' => 2],
    // Programming
    ['name' => 'Python', 'icon' => 'python', 'cat' => 'Programming', 'sort' => 1],
    ['name' => 'Java', 'icon' => 'java', 'cat' => 'Programming', 'sort' => 2],
    ['name' => 'C', 'icon' => 'c', 'cat' => 'Programming', 'sort' => 3],
    ['name' => 'C++', 'icon' => 'cplusplus', 'cat' => 'Programming', 'sort' => 4],
    // Database
    ['name' => 'MySQL', 'icon' => 'mysql', 'cat' => 'Database', 'sort' => 1],
    ['name' => 'SQL', 'icon' => 'sqlite', 'cat' => 'Database', 'sort' => 2], // sqlite as a generic sql fallback
];

foreach ($skills as $s) {
    if (isset($categories[$s['cat']])) {
        $stmt = $db->prepare("INSERT INTO skills (category_id, name, icon_reference, sort_order) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE icon_reference=VALUES(icon_reference)");
        $stmt->execute([$categories[$s['cat']], $s['name'], $s['icon'], $s['sort']]);
    }
}

// Update experience sorting explicitly
$db->query("UPDATE experiences SET sort_order = 1 WHERE organization = 'Code IT Rwanda'");
$db->query("UPDATE experiences SET sort_order = 2 WHERE organization = 'Tresca Hub'");

echo "Phase 6 DB setup complete.";

