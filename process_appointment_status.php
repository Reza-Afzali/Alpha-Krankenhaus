<?php
// --- FILE: process_appointment_status.php ---
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email_functions.php'; // E-Mail-Hilfe einbinden

// Platzhalter-Flash-Funktion (stellen Sie sicher, dass diese verfügbar ist, z. B. in config.php)
if (!function_exists('flash')) {
    function flash($key, $message = null)
    {
        if ($message) {
            $_SESSION['flash'][$key] = $message;
        } else {
            $msg = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
    }
}

// 1. Sicherheitsprüfung
if (empty($_SESSION['user_id'])) {
    flash('error', 'Authentication required.');
    header('Location: index.php');
    exit;
}

// 2. Eingabevalidierung
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || $_POST['action'] !== 'approve') {
    flash('error', 'Invalid request for appointment approval.');
    header('Location: staff.php');
    exit;
}

$appointmentId = (int) $_POST['id'];

try {
    // Starten Sie aus Sicherheitsgründen eine Transaktion.
    $pdo->beginTransaction();

    // 3. Termindetails und aktuellen Status abrufen

    // Alle für die E-Mail benötigten Informationen vor der Aktualisierung abrufen
    $stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->execute([$appointmentId]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        flash('error', 'Appointment record not found.');
        header('Location: staff.php');
        exit;
    }

    // Prüfen, ob der Termin bereits genehmigt wurde (erneutes Senden/Aktualisieren verhindern)
    if ($appointment['status'] === 'Approved') {
        flash('warning', 'Appointment ID ' . $appointmentId . ' is already approved. No action taken.');
        $pdo->commit();
        header('Location: staff.php');
        exit;
    }

    // 4. Terminstatus aktualisieren
    $updateStmt = $pdo->prepare("UPDATE appointments SET status = 'Approved' WHERE id = ?");
    $updateStmt->execute([$appointmentId]);

    // Datenbankänderungen übernehmen
    $pdo->commit();

    // 5. Bestätigungs-E-Mail senden (Dieser Block wird NUR ausgeführt, WENN die Datenbankaktualisierung erfolgreich war)
    $emailSent = sendAppointmentApprovalEmail($appointment);

    if ($emailSent) {
        flash('success', 'Termin erfolgreich **genehmigt** und Bestätigungs-E-Mail versendet an ' . htmlspecialchars($appointment['email']) . '.');
    } else {
        // E-Mail-Fehler protokollieren, aber trotzdem Datenbankerfolg melden
        error_log("E-Mail-Versand für Termin-ID fehlgeschlagen: " . $appointmentId);
        flash('success', 'Termin erfolgreich **genehmigt**. WARNUNG: Bestätigungs-E-Mail konnte nicht versendet werden. Serverprotokolle prüfen.');
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Datenbankfehler in process_appointment_status: " . $e->getMessage());
    flash('error', 'Bei der Genehmigung ist ein Datenbankfehler aufgetreten: ' . $e->getMessage());

} catch (Exception $e) {
    error_log("Allgemeiner Fehler in process_appointment_status: " . $e->getMessage());
    flash('error', 'Es ist ein unerwarteter Fehler aufgetreten: ' . $e->getMessage());
}

header('Location: staff.php');
exit;