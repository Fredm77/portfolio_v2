<?php

require_once __DIR__ . '/bootstrap.php';

class Database
{
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $host = config_env('DB_HOST');
            $database = config_env('DB_NAME');
            $username = config_env('DB_USER');
            $password = config_env('DB_PASSWORD');

            if ($host === null || $database === null || $username === null || $password === null) {
                throw new RuntimeException('Database configuration is incomplete.');
            }

            $this->conn = new PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4",
                $username,
                $password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (Throwable $exception) {
            error_log('Database connection error: ' . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>
