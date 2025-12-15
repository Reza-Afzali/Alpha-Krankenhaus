<?php
// PHP-Fehlerberichterstattung (NUR ZUM DEBUGGING AUFBEWAHREN)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Konfiguration und Datenbank laden
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Setze den Header für AJAX-Antworten auf Klartext
header('Content-Type: text/plain');
// Prüfen, ob das Formular per POST übermittelt wurde und den erforderlichen Parameter 'action' enthält
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {

    // 2. Datenvalidierung
    $errors = [];

    // Variablen zuweisen und bereinigen
    $fullName = trim($_POST['full_name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $dob = trim($_POST['dob'] ?? '');
    $role = trim($_POST['role'] ?? '');


    // Geburtsdatum-/Altersbestätigung (18+ und keine zukünftigen Daten)
    if (empty($errors) && !empty($dob)) {
        try {
            $dob_date = new DateTime($dob);
            $now = new DateTime();
            $age_limit = 18;

            // 1. Überprüfung des zukünftigen Datums (Kurzmeldung)
            if ($dob_date > $now) {
                $errors[] = "Geburtsdatum liegt in der Zukunft.";
            }

            // 2. Altersprüfung (mindestens 18 Jahre alt) (Kurzfassung)
            $min_age_date = clone $dob_date;
            $min_age_date->modify("+{$age_limit} years");

            if ($min_age_date > $now) {
                $errors[] = "Muss mindestens 18 Jahre alt sein.";
            }

        } catch (\Exception $e) {
            $errors[] = "Ungültiges Datumsformat.";
        }
    }

    // Falls bisher keine Validierungsfehler aufgetreten sind, fahren Sie mit den Datenbankprüfungen fort.
    if (empty($errors)) {

        try {
            // 3. Passwort-Hashing
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // 4. Prüfen, ob eine E-Mail-Adresse existiert
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt_check->execute([':email' => $email]);

            if ($stmt_check->fetchColumn() > 0) {
                // Verwenden Sie den Fehlercode 409 Conflict für Ressourcenduplizierung
                http_response_code(409);
                // Kurznachricht
                echo "E-Mail ist bereits registriert.";
                exit();
            } else {

                // 5. Einfügen von ner user SQL
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

                // Erfolg! Sende 200 OK. 
                http_response_code(200);
                echo "Erfolgreich registriert. Wird neu geladen...";
                //header('Location: admin.php'); um sicherzustellen, dass die AJAX-Erfolgsbehandlung funktioniert.
                exit();
            }

        } catch (PDOException $e) {
            // Datenbankfehler
            http_response_code(500);
            echo "Datenbankfehler."; // Allgemeiner, kurzer Fehler
            exit();
        }
    }

    // Falls Validierungsfehler auftreten, wird der Statuscode 400 (Bad Request) gesendet.
    if (!empty($errors)) {
        http_response_code(400);
        // Senden Sie nur den ERSTEN Fehler als kurze Toast-Nachricht.
        echo $errors[0];
        exit();
    }

} else {
    http_response_code(403);
    echo "Unbefugter Zugriff.";
    exit();
}
?>