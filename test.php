<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=alphahospital;charset=utf8mb4", "root", "");
    echo "Database connected!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
