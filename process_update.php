<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

// --- 1. Sicherheitsüberprüfung ---
// Nur POST-Anfragen und angemeldete Administratoren dürfen Updates verarbeiten
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    flash('error', 'Unbefugter Zugriff oder ungültige Anfrage.');
    header('Location: admin.php');
    exit;
}

// --- 2. Eingabe bereinigen und validieren ---
$type = $_POST['type'] ?? '';
$id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);

if (!$id || !in_array($type, ['appointment', 'message'])) {
    flash('error', 'Ungültiger Datensatztyp oder ID für die Aktualisierung.');
    header('Location: admin.php');
    exit;
}

$tableName = ($type === 'appointment') ? 'appointments' : 'messages';
$updateSuccessful = false;

try {
    if ($type === 'appointment') {
        // --- Termin-Daten verarbeiten ---
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $phone = trim($_POST['phone'] ?? '');
        $appt_date = trim($_POST['appt_date'] ?? '');
        $appt_time = trim($_POST['appt_time'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $doctor = trim($_POST['doctor'] ?? '');
        $reason = trim($_POST['reason'] ?? '');
        // NEU: Status-Feld hinzugefügt
        $status = trim($_POST['status'] ?? 'Pending'); 
        
        if (!$email || !$first_name || !$appt_date || !$appt_time) {
            flash('error', 'Erforderliche Termin-Felder fehlen.');
            header('Location: edit_form.php?type=appointment&id=' . $id);
            exit;
        }

        // SQL UPDATE Anweisung für Termine
        // NEU: status = :status zur SQL-Abfrage hinzugefügt
        $sql = "UPDATE appointments SET 
                    first_name = :first_name, 
                    last_name = :last_name, 
                    email = :email, 
                    phone = :phone, 
                    appt_date = :appt_date, 
                    appt_time = :appt_time, 
                    department = :department, 
                    doctor = :doctor, 
                    reason = :reason,
                    status = :status 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':phone' => $phone,
            ':appt_date' => $appt_date,
            ':appt_time' => $appt_time,
            ':department' => $department,
            ':doctor' => $doctor,
            ':reason' => $reason,
            ':status' => $status, // NEU: Status-Wert übergeben
            ':id' => $id
        ]);
        
        $updateSuccessful = true;

    } elseif ($type === 'message') {
        // --- Nachrichten-Daten verarbeiten ---
        $name = trim($_POST['name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (!$email || !$name || !$subject || !$message) {
            flash('error', 'Erforderliche Nachrichten-Felder fehlen.');
            header('Location: edit_form.php?type=message&id=' . $id);
            exit;
        }

        // SQL UPDATE Anweisung für Nachrichten
        $sql = "UPDATE messages SET 
                    name = :name, 
                    email = :email, 
                    subject = :subject, 
                    message = :message 
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':subject' => $subject,
            ':message' => $message,
            ':id' => $id
        ]);
        
        $updateSuccessful = true;
    }

    // --- 3. Feedback geben und weiterleiten ---
    if ($updateSuccessful) {
        if ($stmt->rowCount() > 0) {
            flash('success', ucfirst($type) . ' Datensatz (ID: ' . $id . ') erfolgreich aktualisiert!');
        } else {
            flash('warning', ucfirst($type) . ' Datensatz (ID: ' . $id . ') gespeichert. Es wurden keine Änderungen vorgenommen.');
        }
    }

} catch (PDOException $e) {
    flash('error', 'Während der Aktualisierung ist ein Datenbankfehler aufgetreten.');
}

// Immer zur Administrationsseite zurückleiten
header('Location: admin.php');
exit;

?>