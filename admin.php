<?php
// --- FILE: admin.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';


// --- 1. Sicherheitsprüfung ---
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// EINSCHRÄNKUNG: Nur Administratoren haben Zugriff auf diese Seite
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'Admin') {
    header('Location: staff.php');
    exit;
}

// --- 2. Datenabruf ---
$appointments = $pdo->query("SELECT *, COALESCE(status, 'Pending') as status FROM appointments ORDER BY created_at DESC")->fetchAll();
$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
// Auswahl bestimmter Spalten für die Sicherheit
$users = $pdo->query("
    SELECT 
        user_id AS id,
        full_name AS user_name,
        email,
        role AS user_role,
        created_at
    FROM users
    ORDER BY created_at DESC
")->fetchAll();

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Krankenhaus-Admin - Verwaltungskonsole</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Bestehender Allgemeiner Admin-Stil */
        .admin-main {
            padding: 140px 0 4rem;
            background: var(--bg-light);
            min-height: 100vh;
        }

        .admin-header-section {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 2rem;
            margin-bottom: 2rem;
            border-left: 6px solid var(--color-primary);
        }

        /* Dashboard-Kartenraster */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        /* Zusammenfassungskarte */
        .summary-card {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid var(--color-border-subtle);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .summary-card.active {
            border: 2px solid var(--color-primary);
            box-shadow: 0 0 0 5px rgba(0, 119, 182, 0.2);
        }

        .card-icon {
            font-size: 2.5rem;
            color: var(--color-primary);
            margin-bottom: 0.5rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--color-text-medium);
            margin: 0;
        }

        .card-number {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--color-dark-bg);
            line-height: 1.2;
            margin: 0.25rem 0 0;
        }

        /* Inhaltsbereich/Tabellencontainer */
        .admin-content-area {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .table-section {
            display: none;
            /* Default hidden */
        }

        .table-section.active {
            display: block;
            /* Aktiven Abschnitt anzeigen */
        }

        /* Vorhandene Tabellenstile */
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .admin-table th {
            /* Verwendung von var(--color-primary) für Konsistenz */
            background: var(--color-primary);
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
        }

        .admin-table td {
            padding: 12px;
            border-bottom: 1px solid var(--color-border-subtle);
            font-size: 0.9rem;
            color: var(--color-text-medium);
        }

        .patient-name {
            font-weight: 700;
            color: var(--color-dark-bg);
        }

        /* .data-card entfernt, ersetzt durch .admin-content-area */
        .btn-update,
        .btn-delete {
            padding: 6px 10px;
            font-size: 0.8rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
            white-space: nowrap;
        }

        .btn-update {
            background: var(--color-tertiary);
            color: white;
            border: none;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
        }

        /* Statusabzeichen */
        .status-badge {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 0.8rem;
        }

        /* Farbvariable für genehmigten Status hinzufügen */
        .status-badge-Pending {
            color: var(--color-error);
            background: #fef2f2;
        }

        .status-badge-Approved {
            color: #155724;
            background: #d4edda;
        }

        /* Filterstile */
        .filter-container {
            margin-bottom: 1.5rem;
            background: var(--bg-light);
            padding: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--color-border-subtle);
        }

        .filter-input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--color-border-subtle);
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        /* Modale Stile (Angepasst an die bestehende Struktur) */
        #registerOverlay.appointment-overlay {
            z-index: 1050;
        }

        #registerOverlay .modal-content {
            max-width: 500px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include 'header.php'; ?>

    <?php
    // Logik für Flash-Nachrichten
    
    // Verwendung der Basisklasse '.toast' aus styles.css
    if ($msg = flash('success')) {
        echo '<div class="toast visible auto-dismiss-toast" style="background: var(--color-tertiary, #108779);">' . htmlspecialchars($msg) . '</div>';
    }
    if ($err = flash('error')) {
        echo '<div class="toast visible" style="background:#c82333;">' . htmlspecialchars($err) . '</div>';
    }
    if ($warn = flash('warning')) {
        echo '<div class="toast visible" style="background:#ffc107; color: #333;">' . htmlspecialchars($warn) . '</div>';
    }
    ?>

    <main class="admin-main">
        <div class="container">

            <div class="admin-header-section">
                <h1 style="margin:0; color:var(--color-dark-bg);">Admin-Verwaltungskonsole</h1>
                <p style="margin: 0.5rem 0 1rem; color:var(--color-text-medium);">
                    Verwalten Sie alle Systemdaten. Angemeldet als
                    <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (Rolle:
                    <strong><?= htmlspecialchars($_SESSION['user_role']) ?></strong>).
                </p>
                <button class="btn btn-primary" id="openRegisterModal">Neuen Benutzer Registrieren</button>
            </div>

            <div class="dashboard-grid">

                <div class="summary-card active" data-target="appointments">
                    <i class="fas fa-calendar-check card-icon"></i>
                    <p class="card-title">Termine</p>
                    <p class="card-number"><?= count($appointments) ?></p>
                </div>

                <div class="summary-card" data-target="messages">
                    <i class="fas fa-envelope card-icon"></i>
                    <p class="card-title">Kontaktnachrichten</p>
                    <p class="card-number"><?= count($messages) ?></p>
                </div>

                <div class="summary-card" data-target="users">
                    <i class="fas fa-users-cog card-icon"></i>
                    <p class="card-title">Systembenutzer</p>
                    <p class="card-number"><?= count($users) ?></p>
                </div>
            </div>
            <div class="admin-content-area">

                <div class="table-section active" id="appointments-section">
                    <h2 class="section-title" style="text-align: left;">Termine (<?= count($appointments) ?> Gesamt)
                    </h2>
                    <div class="filter-container">
                        <label for="adminApptFilter">Termine Suchen:</label>
                        <input type="text" id="adminApptFilter" class="filter-input"
                            placeholder="Namen, E-Mail, Abteilung oder Status eingeben...">
                    </div>
                    <table class="admin-table" id="adminApptTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patientenname</th>
                                <th>Kontakt-E-Mail</th>
                                <th>Abteilung</th>
                                <th>Datum/Uhrzeit</th>
                                <th>Status</th>
                                <th>Aktion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appt): ?>
                                <tr>
                                    <td><?= htmlspecialchars($appt['id']) ?></td>
                                    <td class="patient-name">
                                        <?= htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($appt['email']) ?></td>
                                    <td><?= htmlspecialchars($appt['department']) ?></td>
                                    <td><?= htmlspecialchars($appt['appt_date']) ?> @
                                        <?= htmlspecialchars($appt['appt_time']) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-badge-<?= htmlspecialchars($appt['status']) ?>">
                                            <?= htmlspecialchars($appt['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit_form.php?type=appointment&id=<?= $appt['id'] ?>"
                                            class="btn-update">Aktualisieren</a>

                                        <form action="delete_record.php" method="POST"
                                            onsubmit="return confirm('Diesen Termin löschen?');" style="display:inline;">
                                            <input type="hidden" name="type" value="appointment">
                                            <input type="hidden" name="id" value="<?= $appt['id'] ?>">
                                            <button type="submit" class="btn-delete">Löschen</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-section" id="messages-section">
                    <h2 class="section-title" style="text-align: left;">Kontaktnachrichten (<?= count($messages) ?>
                        Gesamt)</h2>
                    <div class="filter-container">
                        <label for="adminMsgFilter">Nachrichten Suchen:</label>
                        <input type="text" id="adminMsgFilter" class="filter-input"
                            placeholder="Namen, Betreff oder Nachrichtentext eingeben...">
                    </div>
                    <table class="admin-table" id="adminMsgTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Betreff</th>
                                <th>Nachricht</th>
                                <th>Datum</th>
                                <th>Aktion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr>
                                    <td><?= htmlspecialchars($msg['id']) ?></td>
                                    <td class="patient-name"><?= htmlspecialchars($msg['name']) ?></td>
                                    <td><?= htmlspecialchars($msg['subject']) ?></td>
                                    <td><?= htmlspecialchars(substr($msg['message'], 0, 30)) ?><?= (strlen($msg['message']) > 30) ? '...' : '' ?>
                                    </td>
                                    <td><?= htmlspecialchars($msg['created_at']) ?></td>
                                    <td>
                                        <a href="edit_form.php?type=message&id=<?= $msg['id'] ?>"
                                            class="btn-update">Aktualisieren</a>

                                        <form action="delete_record.php" method="POST"
                                            onsubmit="return confirm('Diese Nachricht löschen?');" style="display:inline;">
                                            <input type="hidden" name="type" value="message">
                                            <input type="hidden" name="id" value="<?= $msg['id'] ?>">
                                            <button type="submit" class="btn-delete">Löschen</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-section" id="users-section">
                    <h2 class="section-title" style="text-align: left;">Systembenutzer (<?= count($users) ?> Gesamt)
                    </h2>
                    <div class="filter-container">
                        <label for="adminUserFilter">Benutzer Suchen:</label>
                        <input type="text" id="adminUserFilter" class="filter-input"
                            placeholder="Benutzernamen, E-Mail oder Rolle eingeben...">
                    </div>
                    <table class="admin-table" id="adminUserTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Benutzername</th>
                                <th>E-Mail</th>
                                <th>Rolle</th>
                                <th>Erstellt Am</th>
                                <th>Aktion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['id']) ?></td>
                                    <td class="patient-name"><?= htmlspecialchars($user['user_name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['user_role']) ?></td>
                                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                                    <td>
                                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn-update">Aktualisieren</a>

                                        <form action="delete_record.php" method="POST"
                                            onsubmit="return confirm('Benutzer <?= $user['user_name'] ?> löschen?');"
                                            style="display:inline;">
                                            <input type="hidden" name="type" value="user">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="btn-delete">Löschen</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="registerOverlay" class="appointment-overlay hidden">
        <div class="appointment-overlay-content login-content">
            <span class="close-btn" id="closeRegister">&times;</span>
            <h3 class="modal-title">Neuen Systembenutzer Registrieren</h3>
            <form action="process_register.php" method="POST" class="book-form" id="registerForm">
                <input type="hidden" name="action" value="register">
                <div class="form-row">
                    <label>Name</label>
                    <input type="text" name="full_name" placeholder="Name des Mitarbeiters" class="validate-me" />
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <label>E-Mail-Adresse</label>
                    <input type="email" name="email" placeholder="beispiel@krankenhaus.com" class="validate-me" />
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <label>Geburtsdatum</label>
                    <input type="date" name="dob" id="reg-dob" class="validate-me" />
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <label>Rolle</label>
                    <select name="role" class="validate-me">
                        <option value="" disabled selected>Rolle auswählen</option>
                        <option value="Staff">Mitarbeiter</option>
                        <option value="Doctor">Arzt</option>
                        <option value="Admin">Admin</option>
                    </select>
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <label>Passwort</label>
                    <input type="password" name="password" id="reg-password" placeholder="••••••••••"
                        class="validate-me" />
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <label>Passwort Bestätigen</label>
                    <input type="password" name="confirm_password" id="reg-confirm-password" placeholder="••••••••••"
                        class="validate-me" />
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <button class="btn btn-primary" type="submit" style="width: 100%;">Konto Erstellen</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /**
* Generische Funktion zum Filtern von Tabellenzeilen anhand eines Eingabefelds.

* @param {string} inputId Die ID des Texteingabefelds.

* @param {string} tableId Die ID des Tabellenelements.

*/
            function setupTableFilter(inputId, tableId) {
                const input = document.getElementById(inputId);
                const table = document.getElementById(tableId);
                if (!input || !table) return;

                const tbody = table.querySelector('tbody');
                if (!tbody) return;
                const rows = tbody.getElementsByTagName('tr');

                input.addEventListener('keyup', function () {
                    const filter = input.value.toUpperCase();

                    // Alle Tabellenzeilen durchlaufen
                    for (let i = 0; i < rows.length; i++) {
                        let rowVisible = false;
                        const cells = rows[i].getElementsByTagName('td');

                        // Alle Zellen durchlaufen, um eine Übereinstimmung zu finden
                        for (let j = 0; j < cells.length; j++) {
                            // Überspringe die letzte Zelle (Aktionsspalte) für ALLE Tabellen, um zu verhindern, 
                            // dass nach dem Text der Schaltfläche „Aktualisieren/Löschen“ gefiltert wird.
                            if (j === cells.length - 1) {
                                continue;
                            }

                            const cellText = cells[j].textContent || cells[j].innerText;
                            if (cellText.toUpperCase().indexOf(filter) > -1) {
                                rowVisible = true;
                                break;
                            }
                        }

                        if (rowVisible) {
                            rows[i].style.display = '';
                        } else {
                            rows[i].style.display = 'none';
                        }
                    }
                });
            }

            // Filter für alle Admin-Tabellen initialisieren
            setupTableFilter('adminApptFilter', 'adminApptTable');
            setupTableFilter('adminMsgFilter', 'adminMsgTable');
            setupTableFilter('adminUserFilter', 'adminUserTable');


            // Logik zum Wechseln von Dashboard-Karten/Registerkarten //  Dashboard Card/Tab Switching Logic
            const summaryCards = document.querySelectorAll('.summary-card');
            const tableSections = document.querySelectorAll('.table-section');

            // Funktion zum Umschalten des aktiven Inhalts
            function switchContent(targetId) {
                // Alle Karten und Abschnitte deaktivieren
                summaryCards.forEach(card => card.classList.remove('active'));
                tableSections.forEach(section => section.classList.remove('active'));

                // Aktivieren Sie die entsprechende Karte und den entsprechenden Abschnitt.
                const activeCard = document.querySelector(`.summary-card[data-target="${targetId}"]`);
                const activeSection = document.getElementById(`${targetId}-section`);

                if (activeCard) activeCard.classList.add('active');
                if (activeSection) activeSection.classList.add('active');
            }

            // Füge Klick-Listener zu Karten hinzu
            summaryCards.forEach(card => {
                card.addEventListener('click', function () {
                    const target = this.getAttribute('data-target');
                    switchContent(target);
                });
            });

            // Modallogik (konsistent gehalten)
            const modal = document.getElementById('registerOverlay');
            const openBtn = document.getElementById('openRegisterModal');
            const closeBtn = document.getElementById('closeRegister');

            if (openBtn && modal) {
                openBtn.addEventListener('click', () => {
                    modal.classList.add('show');
                    modal.classList.remove('hidden');
                });
            }

            const closeModal = () => {
                modal.classList.remove('show');
                modal.classList.add('hidden');
            };

            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }

            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        closeModal();
                    }
                });
            }

            // Logik zum automatischen Ausblenden von Toast-Meldungen 
            // (separat gehalten, da sie PHP-Flash-Meldungen verarbeitet)
            const successToast = document.querySelector('.auto-dismiss-toast');
            if (successToast) {
                const delay = 3500;
                const transitionDuration = 350;
                setTimeout(() => {
                    successToast.classList.remove('visible');
                    setTimeout(() => {
                        successToast.remove();
                    }, transitionDuration);
                }, delay);
            }
        });
    </script>
    <?php require_once __DIR__ . '/footer.php'; ?>
    <script src="script.js"></script>
</body>

</html>