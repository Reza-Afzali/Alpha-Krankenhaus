<?php
// PHP Error Reporting (KEEP THIS FOR DEBUGGING)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Load Configuration AND Database
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Set the header to plain text for AJAX responses
header('Content-Type: text/plain');

// Check if the form was submitted via POST and has the required 'action' parameter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {

    // 2. Data Validation
    $errors = [];

    // Assign and sanitize variables
    $fullName = trim($_POST['full_name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $dob = trim($_POST['dob'] ?? '');
    $role = trim($_POST['role'] ?? '');

    // Basic required field checks (Shortened Messages)
    // if (empty($fullName)) {
    //     $errors[] = "Name fehlt.";
    // }
    // if (empty($email)) {
    //     $errors[] = "E-Mail fehlt.";
    // }
    // if (empty($password)) {
    //     $errors[] = "Passwort fehlt.";
    // }
    // if (empty($dob)) {
    //     $errors[] = "Geburtsdatum fehlt.";
    // }
    // if (empty($role)) {
    //     $errors[] = "Rolle fehlt.";
    // }

    // if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //     $errors[] = "Ungültiges E-Mail-Format.";
    // }
    // if ($password !== $confirmPassword) {
    //     $errors[] = "Passwörter stimmen nicht überein.";
    // }

    // DOB/AGE Validation (18+ and No Future Dates)
    if (empty($errors) && !empty($dob)) {
        try {
            $dob_date = new DateTime($dob);
            $now = new DateTime();
            $age_limit = 18;

            // 1. Future Date Check (Shortened Message)
            if ($dob_date > $now) {
                $errors[] = "Geburtsdatum liegt in der Zukunft.";
            }

            // 2. Age Check (must be 18+) (Shortened Message)
            $min_age_date = clone $dob_date;
            $min_age_date->modify("+{$age_limit} years");

            if ($min_age_date > $now) {
                $errors[] = "Muss mindestens 18 Jahre alt sein.";
            }

        } catch (\Exception $e) {
            $errors[] = "Ungültiges Datumsformat.";
        }
    }

    // If no validation errors so far, proceed to database checks
    if (empty($errors)) {

        try {
            // 3. Password Hashing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 4. Check if email exists
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt_check->execute([':email' => $email]);

            if ($stmt_check->fetchColumn() > 0) {
                // Use 409 Conflict for resource duplication
                http_response_code(409);
                // Shortened Message
                echo "E-Mail ist bereits registriert.";
                exit();
            } else {

                // 5. Prepare SQL (unchanged)
                $sql = "INSERT INTO users (full_name, dob, email, role, password_hash, created_at)
                        VALUES (:full_name, :dob, :email, :role, :password_hash, NOW())";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ':full_name' => $fullName,
                    ':dob' => $dob,
                    ':email' => $email,
                    ':role' => $role,
                    ':password_hash' => $hashedPassword,
                ]);

                // Success! Send 200 OK. 
                http_response_code(200);
                echo "Erfolgreich registriert. Wird neu geladen...";
                // 🔴 FIX: REMOVED header('Location: admin.php'); to ensure AJAX success handling works.
                exit();
            }

        } catch (PDOException $e) {
            // Database error
            http_response_code(500);
            echo "Datenbankfehler."; // Generic, short error
            exit();
        }
    }

    // If validation errors occurred, send 400 Bad Request
    if (!empty($errors)) {
        http_response_code(400);
        // Send only the FIRST error for a short toast message.
        echo $errors[0];
        exit();
    }

} else {
    http_response_code(403);
    echo "Unbefugter Zugriff.";
    exit();
}
?>