<?php
// edit_form.php - Allgemeine Bearbeitungsansicht für Termine und Nachrichten
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

// --- 1. Sicherheitsüberprüfung ---
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    flash('error', 'Unbefugter Zugriff. Nur Administratoren dürfen diese Seite aufrufen.');
    header('Location: index.php');
    exit;
}

// --- 2. Eingabe-Parameter abrufen und validieren ---
$type = $_GET['type'] ?? '';
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

// Überprüfen, ob eine gültige ID und ein gültiger Typ vorhanden sind
if (!$id || !in_array($type, ['appointment', 'message'])) {
    flash('error', 'Ungültiger Datensatztyp oder ID ausgewählt.');
    header('Location: admin.php');
    exit;
}

// Tabelle und Datensatznamen festlegen
$tableName = ($type === 'appointment') ? 'appointments' : 'messages';
$recordName = ($type === 'appointment') ? 'Termin' : 'Nachricht';

// --- 3. Datensatz aus der Datenbank abrufen ---
try {
    // Abrufen aller Spalten für den entsprechenden Datensatz
    $stmt = $pdo->prepare("SELECT * FROM $tableName WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        flash('error', "$recordName nicht gefunden.");
        header('Location: admin.php');
        exit;
    }
} catch (PDOException $e) {
    flash('error', "Datenbankfehler beim Abrufen des $recordName.");
    header('Location: admin.php');
    exit;
}

// Zusätzliche Daten für das Termin-Formular (optional, falls benötigt)
$departments = $pdo->query("SELECT DISTINCT department FROM appointments")->fetchAll(PDO::FETCH_COLUMN);
$doctors = $pdo->query("SELECT full_name FROM users WHERE role = 'Doctor'")->fetchAll(PDO::FETCH_COLUMN);
$statuses = ['Pending', 'Approved', 'Cancelled']; // 'Cancelled' hinzugefügt

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= $recordName ?> Bearbeiten (ID: <?= $id ?>)</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS-Stile beibehalten */
        .admin-main { padding: 120px 0 4rem; background: var(--bg-light); min-height: 100vh; }
        .data-card { max-width: 700px; margin: 2rem auto; background: var(--bg-white); border-radius: var(--border-radius); box-shadow: var(--shadow-md); padding: 2rem; }
        .form-row { margin-bottom: 1.5rem; }
        .form-row label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--color-text-dark); }
        .form-row input[type="text"], .form-row select, .form-row input[type="email"], .form-row input[type="date"], .form-row input[type="time"], .form-row textarea { 
            width: 100%; padding: 10px; border: 1px solid var(--color-border-subtle); border-radius: 6px; box-sizing: border-box; font-size: 1rem; 
        }
        .form-row textarea { resize: vertical; min-height: 100px; }
        .form-actions { margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end; }
        .btn-cancel { background: var(--bg-light); color: var(--color-text-medium); border: 1px solid var(--color-border-subtle); padding: 10px 20px; border-radius: 6px; text-decoration: none; cursor: pointer; transition: background 0.2s; }
        .btn-cancel:hover { background: var(--color-border-subtle); }
        .btn-primary { background: var(--color-primary); color: white; border: 1px solid var(--color-primary); padding: 10px 20px; border-radius: 6px; cursor: pointer; transition: background 0.2s; }
        .btn-primary:hover { background: var(--color-primary-dark); }
    </style> 
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="admin-main">
        <div class="container">
            <div class="data-card">
                <h1 class="section-title"><?= $recordName ?> Bearbeiten (ID: <?= $id ?>)</h1>
                <p>Ändern Sie die Details für den ausgewählten <?= $recordName ?>.</p>

                <form action="process_update.php" method="POST">
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

                    <?php if ($type === 'appointment'): ?>
                        
                        <div class="form-row">
                            <label for="first_name">Vorname</label>
                            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($record['first_name']) ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="last_name">Nachname</label>
                            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($record['last_name']) ?>">
                        </div>
                        <div class="form-row">
                            <label for="email">E-Mail</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($record['email']) ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="phone">Telefonnummer</label>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($record['phone']) ?>">
                        </div>
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div>
                                <label for="appt_date">Datum</label>
                                <input type="date" id="appt_date" name="appt_date" value="<?= htmlspecialchars($record['appt_date']) ?>" required>
                            </div>
                            <div>
                                <label for="appt_time">Uhrzeit</label>
                                <input type="time" id="appt_time" name="appt_time" value="<?= htmlspecialchars($record['appt_time']) ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <label for="department">Abteilung</label>
                            <select id="department" name="department" required>
                                <?php foreach ($departments as $dep): ?>
                                    <option value="<?= htmlspecialchars($dep) ?>" <?= ($record['department'] === $dep) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($dep) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-row">
                            <label for="doctor">Arzt (Optional)</label>
                            <select id="doctor" name="doctor">
                                <option value="">Kein spezifischer Arzt</option>
                                <?php foreach ($doctors as $doc): ?>
                                    <option value="<?= htmlspecialchars($doc) ?>" <?= ($record['doctor'] === $doc) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($doc) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                         <div class="form-row">
                            <label for="reason">Grund</label>
                            <textarea id="reason" name="reason"><?= htmlspecialchars($record['reason']) ?></textarea>
                        </div>
                        <div class="form-row">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= htmlspecialchars($status) ?>" <?= ($record['status'] === $status) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php elseif ($type === 'message'): ?>
                        
                        <div class="form-row">
                            <label for="name">Name des Absenders</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($record['name']) ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="email">E-Mail des Absenders</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($record['email']) ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="subject">Betreff</label>
                            <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($record['subject']) ?>" required>
                        </div>
                        <div class="form-row">
                            <label for="message">Nachricht</label>
                            <textarea id="message" name="message" required><?= htmlspecialchars($record['message']) ?></textarea>
                        </div>
                        <?php endif; ?>

                    <div class="form-actions">
                        <a href="admin.php" class="btn-cancel">Abbrechen und Zurück</a>
                        <button type="submit" class="btn-primary"><?= $recordName ?> Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>