<?php
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

// 1. Sicherheitsprüfung: Nur angemeldete Administratoren können Datensätze löschen
// 2. Prüfen, ob Nicht-Administratoren eine Sitzung erhalten.
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('error', 'Ungültige Anfragemethode.');
    header('Location: admin.php');
    exit;
}

$type = $_POST['type'] ?? '';
$id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);

// 3. Aktualisieren Sie die Validierungsliste, um 'user' einzuschließen.
if (!$id || !in_array($type, ['appointment', 'message', 'user'])) {
    flash('error', 'Ungültige Datensatzparameter.');
    header('Location: admin.php');
    exit;
}

try {
    // 4. Aktualisieren Sie die Zuordnung, um die Tabelle „Benutzer“ einzuschließen.

    switch ($type) {
        case 'appointment':
            $tableName = 'appointments';
            break;
        case 'message':
            $tableName = 'messages';
            break;
        case 'user':
            $tableName = 'users';
            // Verhindern, dass Administratoren ihr eigenes Konto löschen, während sie angemeldet sind
            if ($id == $_SESSION['user_id']) {
                flash('error', 'Sie können Ihr eigenes Benutzerkonto nicht löschen.');
                header('Location: admin.php');
                exit;
            }
            break;
        default:
            flash('error', 'Invalid deletion type.');
            header('Location: admin.php');
            exit;
    }

    // 5. Löschvorgang vorbereiten und ausführen

    // HINWEIS: Für die Benutzertabelle wird „user_id“ und für alle anderen Tabellen „id“ verwendet.
    $id_field = ($type === 'user') ? 'user_id' : 'id';

    $stmt = $pdo->prepare("DELETE FROM $tableName WHERE $id_field = :id");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() > 0) {
        flash('success', 'Der  ' . $type . ' Datensatz wurde erfolgreich gelöscht.');
    } else {
        flash('error', 'Datensatz nicht gefunden oder bereits gelöscht.');
    }

} catch (PDOException $e) {
    flash('error', 'Database error: ' . $e->getMessage());
}

// 6. Weiterleitung zurück zum Management-Dashboard
header('Location: admin.php');
exit;