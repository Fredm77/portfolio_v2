<?php
require_once 'config/database.php';
$db = (new Database())->getConnection();

// Add missing columns to projects table if they don't exist
$columns = [
    'technologies' => 'VARCHAR(255)',
    'role' => 'VARCHAR(100)',
    'problem' => 'TEXT',
    'solution' => 'TEXT',
    'key_features' => 'TEXT',
    'challenges' => 'TEXT',
    'results' => 'TEXT',
    'year' => 'VARCHAR(20)'
];

foreach ($columns as $col => $type) {
    try {
        $db->exec("ALTER TABLE projects ADD COLUMN $col $type");
        echo "Added column $col\n";
    } catch (PDOException $e) {
        // Column likely already exists
    }
}

// Update the initial projects with their technologies so we can remove the hardcoded PHP logic
$db->exec("UPDATE projects SET technologies = 'React,Node.js,MySQL' WHERE slug = 'umuhinzi'");
$db->exec("UPDATE projects SET technologies = 'PHP,MySQL,Bootstrap,JavaScript', role = 'Lead Developer', problem = 'Manual spreadsheets were causing data loss and taking hours to reconcile daily.', solution = 'A centralized digital reporting system with role-based access.', year = '2025' WHERE slug = 'tresca-engineering-daily-reporting'");

echo "DB Schema for Projects updated successfully.\n";

