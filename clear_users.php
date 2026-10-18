<?php
require_once 'config/database.php';
$db = (new Database())->getConnection();
$db->query("DELETE FROM users");
echo "Users table cleared.";

