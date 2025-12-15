<?php
// fetch_booked_slots.php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Stellen Sie sicher, dass die Parameter vorhanden sind
$date = $_GET['date'] ?? null;
$doctor = $_GET['doctor'] ?? null;

if (!$date || !$doctor) {
    // Wenn Datum oder Arzt fehlen, geben Sie eine leere Liste zurück (alle Slots verfügbar)
    echo json_encode([]);
    exit;
}

// Holen Sie alle gebuchten Zeiten für das Datum und den Arzt
try {
    $stmt = $pdo->prepare("
        SELECT appt_time FROM appointments 
        WHERE appt_date = :date AND doctor = :doctor
    ");
    $stmt->execute([':date' => $date, ':doctor' => $doctor]);
    $booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Geben Sie die Liste der gebuchten Zeiten als JSON zurück
    echo json_encode($booked_slots);

} catch (PDOException $e) {
    // Im Fehlerfall eine leere Liste zurückgeben
    error_log("Availability fetch DB Error: " . $e->getMessage());
    echo json_encode([]);
}
?>