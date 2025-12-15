<?php
// edit_user.php - Benutzerrolle bearbeiten
session_start();
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';

// 1. Sicherheitsüberprüfung: Nur Admin sollte darauf zugreifen
if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    // Verwendung der flash-Funktion (angenommen in config.php definiert)
    flash('error', 'Unbefugter Zugriff. Nur Administratoren dürfen diese Seite aufrufen.');
    header('Location: index.php');
    exit;
}

// 2. Benutzer-ID abrufen und validieren
$user_id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

if (!$user_id) {
    flash('error', 'Ungültige Benutzer-ID ausgewählt.');
    header('Location: admin.php');
    exit;
}

try {
    // 3. Benutzerdatensatz abrufen
    $stmt = $pdo->prepare("SELECT user_id, full_name, email, role FROM users WHERE user_id = :id");
    $stmt->execute([':id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        flash('error', 'Benutzer nicht gefunden.');
        header('Location: admin.php');
        exit;
    }
} catch (PDOException $e) {
    flash('error', 'Datenbankfehler beim Abrufen des Benutzerdatensatzes.');
    header('Location: admin.php');
    exit;
}

// Verfügbare Rollen
$roles = ['Admin', 'Doctor', 'Staff'];
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Benutzerrolle Bearbeiten</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS-Stile beibehalten */
        .admin-main { padding: 120px 0 4rem; background: var(--bg-light); min-height: 100vh; }
        .data-card { max-width: 600px; margin: 2rem auto; background: var(--bg-white); border-radius: var(--border-radius); box-shadow: var(--shadow-md); padding: 2rem; }
        .form-row { margin-bottom: 1.5rem; }
        .form-row label { display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--color-text-dark); }
        /* Hier wurde input[type="email"] im Original-Code vergessen, aber für Konsistenz hinzugefügt, falls es jemals benötigt wird */
        .form-row input[type="text"], .form-row select, .form-row input[type="email"] { width: 100%; padding: 10px; border: 1px solid var(--color-border-subtle); border-radius: 6px; box-sizing: border-box; font-size: 1rem; }
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
                <h1 class="section-title">Benutzerrolle Bearbeiten (ID: <?= $user_id ?>)</h1>
                <p>Ändern Sie die Rolle für **<?= htmlspecialchars($user['full_name']) ?>** (<?= htmlspecialchars($user['email']) ?>).</p>

                <form action="process_update_user.php" method="POST">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">

                    <div class="form-row">
                        <label for="full_name">Vollständiger Name</label>
                        <input type="text" id="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" disabled>
                    </div>

                    <div class="form-row">
                        <label for="email">E-Mail</label>
                        <input type="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    </div>

                    <div class="form-row">
                        <label for="role">Benutzerrolle</label>
                        <select id="role" name="role" required>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= htmlspecialchars($role) ?>" <?= ($user['role'] === $role) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($role) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-actions">
                        <a href="admin.php" class="btn-cancel">Abbrechen und Zurück</a>
                        <button type="submit" class="btn-primary">Rolle Aktualisieren</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>