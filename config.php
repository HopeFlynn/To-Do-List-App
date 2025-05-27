<?php
// config.php - Database configuration
$host = 'localhost';
$dbname = 'daily_todo';  // Make sure this database exists
$username = 'root';    // Change to your MySQL username
$password = '';        // Change to your MySQL password (if any)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>