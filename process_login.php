<?php
// process_login.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Weiterleitung, falls es sich nicht um eine POST-Anfrage handelt
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
// Stellen Sie sicher, dass Sie die Eingabenamen aus dem HTML-Anmeldeformular verwenden: login_email und login_password
$email = filter_var(trim($_POST['login_email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = $_POST['login_password'] ?? '';

if (!$email || empty($password)) {
    flash('error', 'Please enter a valid email and password.');
    header('Location: index.php');
    exit;
}

$login_error_message = 'Invalid email or password.';

try {
    // Sicherstellen, dass für die Sitzungsnutzung die Rolle „Rolle“ ausgewählt ist
    $stmt = $pdo->prepare('SELECT user_id, full_name, role, password_hash FROM users WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role']; // Rolle speichern
        $_SESSION['is_logged_in'] = true;

        flash('success', 'Welcome back, ' . htmlspecialchars($user['full_name']) . '!');
        // Rolle auf Weiterleitung prüfen
        if ($user['role'] === 'Admin') {
            header('Location: admin.php');
        } else {
            // Alle anderen Rollen (Mitarbeiter, Arzt, Patient) werden an die Mitarbeiterkonsole weitergeleitet.
            header('Location: staff.php');
        }
        exit; // Bei Erfolg Weiterleitung zu admin.php
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