<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "Database connection successful!\n";
    
    // Quick test to verify tables
    $stmt = $db->query("SHOW TABLES");
    echo "Tables in database:\n";
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo "- " . $row[0] . "\n";
    }
} else {
    echo "Database connection failed!\n";
}
?>

