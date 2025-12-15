<?php
// db.php

// DATABASE SETTINGS
$db_host = '127.0.0.1';
$db_name = 'alphahospital';
$db_user = 'root';
$db_pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Fehler intern protokollieren, anstatt sie dem Benutzer in einem Produktionssystem anzuzeigen
    die("Database connection failed: " . $e->getMessage());
}
?>