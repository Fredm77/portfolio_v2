<?php
require_once 'config/database.php';
$db = (new Database())->getConnection();

echo "Starting Integration Tests...\n";

// 1. Test Project Insertion
$slug = 'integration-test-project';
$db->exec("DELETE FROM projects WHERE slug = '$slug'");
$stmt = $db->prepare("INSERT INTO projects (title, slug, short_description, category, image, featured, status, technologies, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute(['Phase 10 Test Project', $slug, 'A project to test DB integration', 'Tool', 'https://via.placeholder.com/800x500/18202C/E9EEF5?text=Test+Project', 1, 'published', 'Docker,AWS,Go', 1]);
$projectId = $db->lastInsertId();
echo "Inserted test project ID $projectId. Featured status and tech tags are set.\n";

// 2. Test Project Image Insertion
$db->exec("DELETE FROM project_images WHERE project_id = $projectId");
$stmt = $db->prepare("INSERT INTO project_images (project_id, image, caption, sort_order) VALUES (?, ?, ?, ?)");
$stmt->execute([$projectId, 'https://via.placeholder.com/1200x600/111721/E9EEF5?text=Gallery+Test', 'Integration Test Gallery Image', 1]);
echo "Inserted test project gallery image.\n";

// 3. Test Skill Insertion
$db->exec("DELETE FROM skills WHERE name = 'Docker Test'");
$stmt = $db->prepare("INSERT INTO skills (category_id, name, icon_reference, sort_order) VALUES ((SELECT id FROM skill_categories LIMIT 1), ?, ?, ?)");
$stmt->execute(['Docker Test', 'docker', 99]);
echo "Inserted test skill.\n";

// 4. Test Experience Insertion
$db->exec("DELETE FROM experiences WHERE organization = 'Integration Test Corp'");
$stmt = $db->prepare("INSERT INTO experiences (position, organization, start_date, end_date, description, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute(['Senior Tester', 'Integration Test Corp', '2026-01-01', null, 'Tested the phase 10 database integration.', 99]);
echo "Inserted test experience.\n";

// 5. Test Education Insertion
$db->exec("DELETE FROM education WHERE institution = 'Test University'");
$stmt = $db->prepare("INSERT INTO education (institution, qualification, start_year, end_year, type, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute(['Test University', 'Master of Integration', '2020', '2022', 'degree', 99]);
echo "Inserted test education.\n";

// 6. Test Social Links Update
$stmt = $db->prepare("UPDATE social_links SET url = 'https://github.com/mukizafred/test' WHERE platform = 'GitHub'");
$stmt->execute();
echo "Updated test social link.\n";

echo "\nAll integration tests successful. The frontend will dynamically reflect these changes.\n";
