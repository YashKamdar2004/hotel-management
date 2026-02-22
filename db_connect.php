<?php
/**
 * Database connection using PDO.
 * Adjust the credentials as needed for your environment.
 */
$host = 'localhost';
$db = 'hbwebsite';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (PDOException $e) {
    // In production you would log this error instead of echoing it.
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
?>
