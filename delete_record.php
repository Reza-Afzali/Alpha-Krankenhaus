<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

// 1. Security Check: Only logged-in admins can delete records
// IMPROVEMENT: Check for Admin role if non-Admin users get a session.
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('error', 'Invalid request method.');
    header('Location: admin.php');
    exit;
}

$type = $_POST['type'] ?? '';
$id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);

// 3. Update the validation list to include 'user'
if (!$id || !in_array($type, ['appointment', 'message', 'user'])) {
    flash('error', 'Invalid record parameters.');
    header('Location: admin.php');
    exit;
}

try {
    // 4. Update the mapping to include the 'users' table
    switch ($type) {
        case 'appointment':
            $tableName = 'appointments';
            break;
        case 'message':
            $tableName = 'messages';
            break;
        case 'user':
            $tableName = 'users';
            // IMPORTANT: Prevent Admin from deleting their own account while logged in
            if ($id == $_SESSION['user_id']) {
                flash('error', 'You cannot delete your own user account.');
                header('Location: admin.php');
                exit;
            }
            break;
        default:
            flash('error', 'Invalid deletion type.');
            header('Location: admin.php');
            exit;
    }

    // 5. Prepare and execute the deletion
    // NOTE: Using 'user_id' for the users table, and 'id' for others.
    $id_field = ($type === 'user') ? 'user_id' : 'id';

    $stmt = $pdo->prepare("DELETE FROM $tableName WHERE $id_field = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        flash('success', 'The ' . $type . ' record has been successfully deleted.');
    } else {
        flash('error', 'Record not found or already deleted.');
    }

} catch (PDOException $e) {
    flash('error', 'Database error: ' . $e->getMessage());
}

// 6. Redirect back to the management dashboard
header('Location: admin.php');
exit;