<?php
require_once 'config/database.php';
$db = (new Database())->getConnection();

// Insert social links
$links = [
    ['platform' => 'GitHub', 'url' => 'https://github.com/mukizafred', 'sort' => 1],
    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com/in/mukizafred', 'sort' => 2],
    ['platform' => 'YouTube', 'url' => 'https://youtube.com/c/mukizafred', 'sort' => 3],
    ['platform' => 'Instagram', 'url' => 'https://instagram.com/mukizafred', 'sort' => 4],
];

foreach ($links as $l) {
    $stmt = $db->prepare("INSERT INTO social_links (platform, url, is_visible, sort_order) VALUES (?, ?, 1, ?) ON DUPLICATE KEY UPDATE url=VALUES(url), is_visible=VALUES(is_visible), sort_order=VALUES(sort_order)");
    $stmt->execute([$l['platform'], $l['url'], $l['sort']]);
}

echo "Socials updated.";

