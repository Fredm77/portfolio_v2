<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$projects = [
    [
        'title' => 'Tresca Engineering Daily Reporting System',
        'slug' => 'tresca-engineering-daily-reporting',
        'short_description' => 'Digital daily reporting and project-control system.',
        'category' => 'Management Systems',
        'featured' => 1,
        'status' => 'published',
        'github_url' => 'https://github.com/#',
        'live_url' => 'https://example.com'
    ],
    [
        'title' => 'Umuhinzi',
        'slug' => 'umuhinzi',
        'short_description' => 'Agricultural information and marketplace platform where farmers can access farming information and sell their products.',
        'category' => 'Agriculture',
        'featured' => 0,
        'status' => 'published',
        'github_url' => 'https://github.com/#',
        'live_url' => 'https://example.com'
    ],
    [
        'title' => 'Newcomer Registration System',
        'slug' => 'newcomer-registration',
        'short_description' => 'Registration and information management system.',
        'category' => 'Management Systems',
        'featured' => 0,
        'status' => 'published',
        'github_url' => '',
        'live_url' => ''
    ]
];

foreach ($projects as $p) {
    $stmt = $db->prepare("INSERT IGNORE INTO projects (title, slug, short_description, category, featured, status, github_url, live_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $p['title'], $p['slug'], $p['short_description'], $p['category'], $p['featured'], $p['status'], $p['github_url'], $p['live_url']
    ]);
}
echo "Projects inserted.\n";

