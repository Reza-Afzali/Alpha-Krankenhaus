<?php
// --- FILE: staff.php (Aktualisiert mit Filtern und Nachrichten-Tabelle) ---
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php'; 

// --- 1. Security Check & Role-Based Access Control (RBAC) ---
if (empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
    header('Location: admin.php');
    exit;
}

// --- 2. Data Fetching ---
// Fetch all Appointments
$appointments = $pdo->query("SELECT *, COALESCE(status, 'Pending') as status FROM appointments ORDER BY created_at DESC")->fetchAll();
// NEU: Fetch all Messages for read-only view
$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();

// Placeholder flash function (ensure this is available, e.g., in config.php)
if (!function_exists('flash')) {
    function flash($key, $message = null) {
        if ($message) {
            $_SESSION['flash'][$key] = $message;
        } else {
            $msg = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Krankenhaus-Personal - Terminverwaltung</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Bestehendes staff.php CSS */
        .staff-main { 
            padding: 140px 0 4rem; 
            background: var(--bg-light); 
            min-height: 100vh; 
        }
        .staff-header-section {
            background: var(--bg-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            padding: 2rem;
            margin-bottom: 3rem;
            border-left: 6px solid var(--color-tertiary); 
        }
        .data-card { 
            background: var(--bg-white); 
            border-radius: var(--border-radius); 
            box-shadow: var(--shadow-md); 
            padding: 2rem; 
            margin-bottom: 2rem; 
        }
        .staff-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 1rem; 
        }
        .staff-table th { 
            background: var(--color-tertiary); 
            color: white; 
            padding: 12px; 
            text-align: left; 
            font-size: 0.85rem;
            text-transform: uppercase;
        }
        .staff-table td { 
            padding: 12px; 
            border-bottom: 1px solid var(--color-border-subtle); 
            font-size: 0.9rem; 
            color: var(--color-text-medium);
        }
        .patient-name { font-weight: 700; color: var(--color-dark-bg); }
        
        /* Approve Button Style */
        .btn-approve {
            background: rgb(0,95,140); 
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 0.85rem;
            border: 1px;
            cursor: pointer;
            transition: background 0.2s;
            white-space: nowrap;
        }
        .btn-approve:hover {
            background: #fff;
            color: rgb(0, 95, 140);
            border-radius: 4px;
            border: 1px solid rgb(0, 95, 140 , 1.0);
            
        }
        /* Status Badges */
        .status-badge { font-weight: bold; padding: 4px 8px; border-radius: 3px; font-size: 0.8rem; }
        .status-badge-Pending { color: var(--color-error); background: #fef2f2; }
        .status-badge-Approved { color: var(--color-success); background: #f0fff4; }
        
        /* NEUE Filter-Eingabestile */
        .filter-container {
            margin-bottom: 2rem;
            background: var(--bg-white);
            padding: 1.5rem 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
        }
        .filter-input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--color-border-subtle);
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <?php
    // Flash messages logic
    if ($msg = flash('success')) {
        // Success messages will auto-dismiss
        echo '<div class="toast visible auto-dismiss-toast" style="position:fixed;left:50%;transform:translateX(-50%);bottom:30px;z-index:9999;background: var(--color-primary);">' . htmlspecialchars($msg) . '</div>';
    }
    if ($err = flash('error')) {
        echo '<div class="toast visible" style="background:#c82333;position:fixed;left:50%;transform:translateX(-50%);bottom:30px;z-index:9999;">' . htmlspecialchars($err) . '</div>';
    }
    if ($warn = flash('warning')) {
        echo '<div class="toast visible" style="background:#ffc107; color: #333; position:fixed;left:50%;transform:translateX(-50%);bottom:30px;z-index:9999;">' . htmlspecialchars($warn) . '</div>';
    }
    ?>

    <main class="staff-main">
        <div class="container">
            
            <div class="staff-header-section">
                <h1 style="margin:0; color:var(--color-dark-bg);">Terminverwaltung für Mitarbeiter</h1>
                <p style="margin: 0.5rem 0 0; color:var(--color-text-medium);">
                    Überprüfen und genehmigen Sie Terminanfragen von Patienten. Sie sind angemeldet als 
                    <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong> (Rolle: 
                    <strong><?= htmlspecialchars($_SESSION['user_role']) ?></strong>).
                </p>
            </div>

            <div class="filter-container">
                <label for="appointmentFilter">Termine Suchen:</label>
                <input type="text" id="appointmentFilter" class="filter-input" placeholder="Namen, E-Mail, Abteilung oder Status eingeben...">
            </div>

            <div class="data-card">
                <h2 class="section-title">Patienten-Terminanfragen (<?= count($appointments) ?> Gesamt)</h2>
                <table class="staff-table" id="appointmentTable">
                    <thead>
                        <tr>
                            <th>Patientenname</th>
                            <th>Kontakt-E-Mail</th>
                            <th>Abteilung</th>
                            <th>Gewünschtes Datum/Uhrzeit</th>
                            <th>Status</th>
                            <th>Grund</th>
                            <th>Aktion</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--color-text-medium); padding: 20px;">
                                    Derzeit sind keine Terminanfragen zur Genehmigung erforderlich.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td class="patient-name"><?= htmlspecialchars($appt['first_name'] . ' ' . $appt['last_name']) ?></td>
                                <td><?= htmlspecialchars($appt['email']) ?></td>
                                <td><?= htmlspecialchars($appt['department']) ?></td> 
                                <td><?= htmlspecialchars($appt['appt_date']) ?> @ <?= htmlspecialchars($appt['appt_time']) ?></td>
                                <td>
                                    <span class="status-badge status-badge-<?= htmlspecialchars($appt['status']) ?>">
                                        <?= htmlspecialchars($appt['status']) ?>
                                    </span>
                                </td> 
                                <td><?= htmlspecialchars(substr($appt['reason'], 0, 50)) ?><?= (strlen($appt['reason']) > 50) ? '...' : '' ?></td>
                                <td>
                                    <?php if ($appt['status'] === 'Pending'): ?>
                                        <form action="process_appointment_status.php" method="POST">
                                            <input type="hidden" name="id" value="<?= $appt['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn-approve">Genehmigen</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: var(--color-success); font-weight: 600;">Genehmigt</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="data-card">
                <h2 class="section-title">Kontakt-Nachrichten von Patienten (<?= count($messages) ?> Gesamt)</h2>
                <div class="filter-container" style="margin-bottom:1rem; box-shadow:none; padding:1.5rem 0;">
                    <label for="messageFilter">Nachrichten Suchen:</label>
                    <input type="text" id="messageFilter" class="filter-input" placeholder="Namen, Betreff oder Nachrichtentext eingeben...">
                </div>

                <table class="staff-table" id="messageTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Betreff</th>
                            <th>Nachrichtenausschnitt</th>
                            <th>Empfangen Am</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--color-text-medium); padding: 20px;">
                                    Keine Kontaktnachrichten gefunden.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td class="patient-name"><?= htmlspecialchars($msg['name']) ?></td>
                                <td><?= htmlspecialchars($msg['email']) ?></td>
                                <td><?= htmlspecialchars($msg['subject']) ?></td>
                                <td><?= htmlspecialchars(substr($msg['message'], 0, 50)) ?><?= (strlen($msg['message']) > 50) ? '...' : '' ?></td>
                                <td><?= htmlspecialchars($msg['created_at']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>


        </div>
    </main>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        /**
         * Generic function to filter table rows based on an input field.
         * @param {string} inputId The ID of the text input field.
         * @param {string} tableId The ID of the table element.
         */
        function setupTableFilter(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            if (!input || !table) return;

            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            input.addEventListener('keyup', function() {
                const filter = input.value.toUpperCase();

                // Check for placeholder rows
                if (rows.length === 1 && rows[0].querySelector('td').textContent.trim().startsWith('Derzeit sind keine')) {
                    return; 
                }
                // Check for placeholder rows in messages table
                 if (rows.length === 1 && rows[0].querySelector('td').textContent.trim().startsWith('Keine Kontaktnachrichten')) {
                    return; 
                }

                for (let i = 0; i < rows.length; i++) {
                    let rowVisible = false;
                    const cells = rows[i].getElementsByTagName('td');
                    
                    // Check all cells in the row for a match
                    for (let j = 0; j < cells.length; j++) {
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

        // Initialize filters for the tables
        setupTableFilter('appointmentFilter', 'appointmentTable');
        setupTableFilter('messageFilter', 'messageTable');
        
        // Auto-dismiss toast logic
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


    <?php include 'footer.php'; ?>
</body>
</html>