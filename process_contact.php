<?php
require_once __DIR__ . '/config.php';

$name = trim($_POST['name'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    flash('error', 'Please fill in all required fields.');
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'contact.php'));
    exit;
}

$stmt = $pdo->prepare("INSERT INTO messages (name,email,subject,message) VALUES (:name,:email,:subject,:message)");
$stmt->execute([
    ':name' => $name,
    ':email' => $email,
    ':subject' => $subject,
    ':message' => $message
]);

flash('success', 'Message Sent Successfully!');
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'contact.php'));
exit;
