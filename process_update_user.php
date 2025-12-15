<?php
// process_update_user.php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

// 1. Security Check: Only Admin should access this
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    flash('error', 'Unauthorized access.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

// 2. Retrieve and Validate Input
$user_id = filter_var($_POST['user_id'] ?? 0, FILTER_VALIDATE_INT);
$new_role = trim($_POST['role'] ?? '');

$allowed_roles = ['Admin', 'Doctor', 'Staff'];

if (!$user_id || !in_array($new_role, $allowed_roles)) {
    flash('error', 'Invalid user ID or role provided.');
    header('Location: admin.php');
    exit;
}

// IMPORTANT: Do not allow an admin to change their own role here 
// to prevent accidental lockout, unless explicitly desired.
// For now, we will allow it, but recommend warning the user in edit_user.php.

try {
    // 3. Update the user's role in the database
    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE user_id = :id");
    $stmt->execute([
        ':role' => $new_role,
        ':id' => $user_id
    ]);

    if ($stmt->rowCount() > 0) {
        flash('success', 'User ID ' . $user_id . ' role successfully updated to **' . htmlspecialchars($new_role) . '**.');
    } else {
        flash('warning', 'User ID ' . $user_id . ' role was not changed (possibly already set to that role).');
    }

} catch (PDOException $e) {
    flash('error', 'Database error while updating user role: ' . $e->getMessage());
}

header('Location: admin.php');
exit;