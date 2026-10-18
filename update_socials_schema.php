<?php
require_once 'config/database.php';

$db = (new Database())->getConnection();
if (!$db) {
    exit("Database connection failed.\n");
}

try {
    // Keep the latest saved value for each platform before enforcing uniqueness.
    $db->exec('DELETE duplicate FROM social_links duplicate INNER JOIN social_links newer ON newer.platform = duplicate.platform AND newer.id > duplicate.id');

    $indexExists = $db->query("SHOW INDEX FROM social_links WHERE Key_name = 'unique_social_platform'")->fetch();
    if (!$indexExists) {
        $db->exec('ALTER TABLE social_links ADD UNIQUE KEY unique_social_platform (platform)');
    }

    echo "Social Links schema updated successfully.\n";
} catch (Throwable $exception) {
    throw $exception;
}