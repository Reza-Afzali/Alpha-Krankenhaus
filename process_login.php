<?php
// process_login.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php'; 

// Redirect if not a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
header('Location: index.php');
exit;
}

// Ensure you use the input names from the HTML login form: login_email and login_password
$email = filter_var(trim($_POST['login_email'] ?? ''), FILTER_VALIDATE_EMAIL); 
$password = $_POST['login_password'] ?? ''; 

if (!$email || empty($password)) {
flash('error', 'Please enter a valid email and password.');
 header('Location: index.php');
 exit;
}

$login_error_message = 'Invalid email or password.';

try {
 // Ensure 'role' is selected for session use
 $stmt = $pdo->prepare('SELECT user_id, full_name, role, password_hash FROM users WHERE email = :email');
 $stmt->execute([':email' => $email]);
 $user = $stmt->fetch();

 if ($user && password_verify($password, $user['password_hash'])) {
 $_SESSION['user_id'] = $user['user_id'];
 $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role']; // Store the role
 $_SESSION['is_logged_in'] = true;

flash('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');
 // Check role for redirection
if ($user['role'] === 'Admin') {
    header('Location: admin.php');
} else {
    // Redirect all other roles (Staff, Doctor, Patient) to the staff console
    header('Location: staff.php'); 
}
exit; // Redirect to admin.php on success
 exit;
 } else {
flash('error', $login_error_message);
 header('Location: index.php');
 exit;
 }
} catch (PDOException $e) {
flash('error', 'A database error occurred during login. Please try again.');
header('Location: index.php');
exit;
}