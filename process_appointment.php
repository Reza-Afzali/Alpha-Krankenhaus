<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Header für AJAX-Antwort festlegen
header('Content-Type: text/plain');

// Prüfen, ob PDO verfügbar ist
if (!isset($pdo)) {
    http_response_code(500);
    echo "Fatal Error: Database connection object (\$pdo) not initialized.";
    exit;
}

// 1. Einfache serverseitige Validierung und Bereinigung
// 'doctor' muss nun die Verfügbarkeit von Slots prüfen.
$required = ['first_name', 'last_name', 'email', 'appt_date', 'appt_time', 'reason', 'doctor'];
foreach ($required as $r) {
    if (empty($_POST[$r])) {
        http_response_code(400); // Verwenden Sie den Statuscode 400 für einen Fehler bei der clientseitigen Validierung.
        echo "Please fill in all required fields.";
        exit;
    }
}

$first = trim($_POST['first_name']);
$last = trim($_POST['last_name']);
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$date = trim($_POST['appt_date']);
$time = trim($_POST['appt_time']);
$department = isset($_POST['department']) ? trim($_POST['department']) : null;
$doctor = trim($_POST['doctor']);
$reason = trim($_POST['reason']);

if (!$email) {
    http_response_code(400);
    echo "Please provide a valid email.";
    exit;
}

// --- DATUMSVALIDIERUNG (Nächster Tag, Montag-Freitag) ---
try {
    $appt_datetime = new DateTime($date);
    $tomorrow = new DateTime('tomorrow');

    // 1. Das Datum muss mindestens morgen sein
    if ($appt_datetime < $tomorrow) {
        http_response_code(400);
        echo "Termine müssen mindestens ab morgen gebucht werden.";
        exit;
    }

    // 2. Das Datum muss ein Wochentag (Montag bis Freitag) sein
    $day_of_week = (int) $appt_datetime->format('w'); // 0 = Sonntag, 6 = Samstag

    if ($day_of_week === 0 || $day_of_week === 6) {
        http_response_code(400);
        echo "Termine können nur von Montag bis Freitag gebucht werden.";
        exit;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo "Ungültiges Datumsformat.";
    exit;
}
// --- Ende DATUMSVALIDIERUNG ---


// --- NEUE SERVER-SEITIGE DUPLIKATSPRÜFUNG ---
try {
    // Prüfen, ob für denselben Arzt zum selben Datum und zur selben Zeit bereits ein Termin existiert
    $stmt_check = $pdo->prepare("
        SELECT COUNT(*) FROM appointments 
        WHERE appt_date = :date AND appt_time = :time AND doctor = :doctor
    ");
    $stmt_check->execute([
        ':date' => $date,
        ':time' => $time,
        ':doctor' => $doctor
    ]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        http_response_code(409); // 409 Conflict
        echo "Dieser Termin ist für den ausgewählten Arzt bereits ausgebucht.";
        exit;
    }
} catch (PDOException $e) {
    error_log("Appointment Duplicate Check DB Error: " . $e->getMessage());
    http_response_code(500);
    echo "Internal server error during availability check. Please try again.";
    exit;
}
// --- ENDE DUPLIKATSPRÜFUNG ---


try {
    // 2. In die Datenbank einfügen
    $stmt = $pdo->prepare("
        INSERT INTO appointments (first_name, last_name, email, phone, appt_date, appt_time, department, doctor, reason) 
        VALUES (:first, :last, :email, :phone, :date, :time, :department, :doctor, :reason)
    ");

    $stmt->execute([
        ':first' => $first,
        ':last' => $last,
        ':email' => $email,
        ':phone' => $phone,
        ':date' => $date,
        ':time' => $time,
        ':department' => $department,
        ':doctor' => $doctor,
        ':reason' => $reason
    ]);

    // 3. Erfolgsmeldung für JavaScript
    http_response_code(200); // Erfolgsstatus
    echo "Terminanfrage erfolgreich!";

} catch (PDOException $e) {
    // Detaillierte Fehlerprotokollierung auf Serverseite
    error_log("Termin-DB-Fehler: " . $e->getMessage());
    http_response_code(500); // Internal Server Error
    // Show a generic message to the user
    echo "Es ist ein Datenbankfehler aufgetreten. Bitte versuchen Sie es später erneut.";
    exit;
}

exit;