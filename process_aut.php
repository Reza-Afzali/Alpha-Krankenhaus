<?php
// FIX: Removed session_start() from here because config.php already starts it
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

$action = $_POST['action'] ?? '';

// LOGIN LOGIC
if ($action === 'login') {
    $email = filter_var(trim($_POST['login_email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['login_password'] ?? '';

    $stmt = $pdo->prepare('SELECT user_id, full_name, role, password_hash FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['is_logged_in'] = true;

        flash('success', 'Logged in! Opening admin dashboard.');
        // ... inside LOGIN LOGIC block ...
        if ($user && password_verify($password, $user['password_hash'])) {
            // ... (session variables) ...

            flash('success', 'Logged in! Opening dashboard.');

            // FIX: Role-based redirection
            if ($user['role'] === 'Admin') {
                header('Location: admin.php');
            } else {
                header('Location: staff.php');
            }
            exit;
        }
        // ...
    } else {
        flash('error', 'Invalid email or password.');
        header('Location: index.php');
    }
    exit;
}

// REGISTER LOGIC
if ($action === 'register') {
    $full_name = trim($_POST['full_name'] ?? '');
    $dob = $_POST['dob'] ?? '';
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $role = trim($_POST['role'] ?? 'Patient');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$full_name || !$dob || !$email || !$role || !$password || !$confirm) {
        flash('error', 'All fields are required.');
        header('Location: index.php');
        exit;
    }

    if ($password !== $confirm) {
        flash('error', 'Passwords do not match.');
        header('Location: index.php');
        exit;
    }

    $stmt_check = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
    $stmt_check->execute([':email' => $email]);
    if ($stmt_check->rowCount() > 0) {
        flash('error', 'Email already exists.');
        header('Location: index.php');
        exit;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    // FIX: Added created_at and NOW()
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, dob, role, created_at) VALUES (:name, :email, :pass, :dob, :role, NOW())");

    $stmt->execute([
        ':name' => $full_name,
        ':email' => $email,
        ':pass' => $hash,
        ':dob' => $dob,
        ':role' => $role
    ]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $full_name;
    $_SESSION['user_role'] = $role;
    $_SESSION['is_logged_in'] = true;

    flash('success', 'Account created! Redirecting to admin.');
    // ... inside REGISTER LOGIC block ...
    // ... (database insert and session setup) ...

    flash('success', 'Account created! Redirecting to dashboard.');

    // FIX: Role-based redirection for new users
    if ($role === 'Admin') {
        header('Location: admin.php');
    } else {
        header('Location: staff.php');
    }
    exit;
}
exit;

?>