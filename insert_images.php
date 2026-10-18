<?php
require_once 'config/database.php';
$db = (new Database())->getConnection();

// Get Tresca ID
$stmt = $db->prepare("SELECT id FROM projects WHERE slug = 'tresca-engineering-daily-reporting' LIMIT 1");
$stmt->execute();
$project = $stmt->fetch();

if ($project) {
    $projectId = $project['id'];
    
    // Insert dummy images
    $images = [
        ['image' => 'https://via.placeholder.com/800x500/18202C/E9EEF5?text=Dashboard+Screenshot', 'caption' => 'Main Dashboard', 'sort_order' => 1],
        ['image' => 'https://via.placeholder.com/800x500/18202C/E9EEF5?text=Reports+View', 'caption' => 'Daily Reports View', 'sort_order' => 2],
        ['image' => 'https://via.placeholder.com/800x500/18202C/E9EEF5?text=Settings+Panel', 'caption' => 'Settings Configuration', 'sort_order' => 3],
    ];
    
    foreach ($images as $img) {
        $insert = $db->prepare("INSERT INTO project_images (project_id, image, caption, sort_order) VALUES (?, ?, ?, ?)");
        $insert->execute([$projectId, $img['image'], $img['caption'], $img['sort_order']]);
    }
    echo "Images inserted for Tresca Engineering.";
} else {
    echo "Project not found.";
}

