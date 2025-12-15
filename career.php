<?php
// career.php - Karriereseite
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/header.php'; // Header einbinden

// --- 1. Datenbankabfrage simulieren (ersetzen Sie dies durch echte DB-Abfrage) ---
try {
    // Echte Abfrage, wenn die 'jobs'-Tabelle existiert und mit Daten gefüllt ist
    // $jobs = $pdo->query("SELECT * FROM jobs WHERE is_active = TRUE ORDER BY department, title")->fetchAll(PDO::FETCH_ASSOC);
    
    // --- Musterdaten (zum Testen, wenn die DB-Tabelle noch leer ist) ---
    $jobs = [
        [
            'job_id' => 101,
            'title' => 'Fachärzt*in für Innere Medizin (Kardiologie)',
            'department' => 'Kardiologie',
            'location' => 'Musterstadt',
            'description' => 'Sie sind verantwortlich für die umfassende Betreuung von Patienten mit kardiovaskulären Erkrankungen. Dies umfasst die Durchführung und Auswertung von Echokardiographien, EKG-Diagnostik und die Betreuung auf unserer Intensivstation.',
            'requirements' => 'Abgeschlossene Facharztausbildung in Innerer Medizin und Kardiologie. Hohe soziale Kompetenz und Teamfähigkeit. Bereitschaft zur Teilnahme am Bereitschaftsdienst.',
            'benefits' => 'Überdurchschnittliche Vergütung nach Tarifvertrag (TV-Ärzte). Betriebliche Altersvorsorge. Umfassende Fort- und Weiterbildungsmöglichkeiten. Ein engagiertes Team und eine moderne Geräteausstattung.',
            'type' => 'Vollzeit'
        ],
        [
            'job_id' => 102,
            'title' => 'Examinierte Pflegefachkraft (IMC/Intensiv)',
            'department' => 'Pflegedienst/Intensivstation',
            'location' => 'Musterstadt',
            'description' => 'Sie übernehmen die ganzheitliche und patientenorientierte Pflege und Überwachung von kritisch kranken Patienten im IMC- oder Intensivbereich unter Einhaltung der Pflegestandards und -richtlinien.',
            'requirements' => 'Abgeschlossene Ausbildung als examinierte Pflegefachkraft. Idealerweise Fachweiterbildung für Intensivpflege oder entsprechende Erfahrung. Einfühlungsvermögen und Belastbarkeit.',
            'benefits' => 'Attraktive Zulagen für Schicht- und Wochenenddienste. Flexible Arbeitszeitmodelle (Vollzeit/Teilzeit möglich). 30 Tage Urlaub. Jobticket oder kostenlose Parkmöglichkeiten.',
            'type' => 'Teilzeit'
        ],
        [
            'job_id' => 103,
            'title' => 'Medizinische/r Fachangestellte/r (MFA)',
            'department' => 'Verwaltung/Ambulanz',
            'location' => 'Musterstadt',
            'description' => 'Sie unterstützen unser Ärzteteam im ambulanten Bereich. Zu Ihren Aufgaben gehören die Patientenaufnahme, Terminplanung, Dokumentation und Durchführung einfacher medizinischer Maßnahmen (z.B. Blutentnahme).',
            'requirements' => 'Abgeschlossene Ausbildung als MFA. Sicherer Umgang mit Praxismanagement-Software. Freundliches und dienstleistungsorientiertes Auftreten.',
            'benefits' => 'Geregelte Arbeitszeiten (keine Nachtdienste). Vergütung nach MFA-Tarif. Möglichkeit zur Übernahme von Verantwortung in der Teamorganisation. Gutes Arbeitsklima in einem interdisziplinären Team.',
            'type' => 'Vollzeit'
        ]
    ];
} catch (PDOException $e) {
    // Fehlerbehandlung bei Datenbankproblemen
    $jobs = [];
    flash('error', 'Datenbankfehler beim Laden der Stellenangebote.');
}

?>

<main class="container main-content career-page">
    <section class="secondary" id="career-intro" style="text-align: center; padding: 40px 0;">
        <h1 class="section-title" style="font-size: 2.5rem; margin-bottom: 10px;">Karriere im Krankenhaus</h1>
        <p style="font-size: 1.1rem; max-width: 800px; margin: 0 auto 30px;">
            Werden Sie Teil unseres Teams! Wir bieten Ihnen die Möglichkeit, in einem modernen und menschlichen Umfeld einen wichtigen Beitrag zur Gesundheitsversorgung zu leisten. Entdecken Sie Ihre Karrieremöglichkeiten in unserem Haus.
        </p>
        <hr>
        <div style="margin-top: 30px;">
            
        </div>
    </section>

    <section id="job-listings" style="padding: 40px 0;">
        <h2 class="section-title" style="margin-bottom: 30px;">Aktuelle Stellenangebote (<?= count($jobs) ?> Positionen)</h2>

        <?php if (empty($jobs)): ?>
            <p style="text-align: center; font-size: 1.1rem; color: #777;">
                Aktuell sind alle Positionen besetzt. Bitte schauen Sie zu einem späteren Zeitpunkt wieder vorbei!
            </p>
        <?php else: ?>
            
            <div class="job-grid" style="display: grid; gap: 25px; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                
                <?php foreach ($jobs as $job): ?>
                    <article class="job-card" style="border: 1px solid var(--color-border-subtle); padding: 25px; border-radius: 8px; box-shadow: var(--shadow-sm); background-color: var(--bg-white);">
                        
                        <div class="job-header" style="border-bottom: 2px solid var(--color-primary); padding-bottom: 15px; margin-bottom: 15px;">
                            <h3 style="color: var(--color-text-dark); margin: 0; font-size: 1.5rem;"><?= htmlspecialchars($job['title']) ?></h3>
                            <p style="margin: 5px 0 0; color: #555; font-size: 0.95rem;">
                                <strong>Abteilung:</strong> <?= htmlspecialchars($job['department']) ?> | 
                                <strong>Ort:</strong> <?= htmlspecialchars($job['location']) ?> | 
                                <strong>Art:</strong> <?= htmlspecialchars($job['type']) ?>
                            </p>
                        </div>

                        <div class="job-details" style="margin-bottom: 20px;">
                            <h4>Ihre Aufgaben</h4>
                            <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                            
                            <h4>Ihr Profil (Anforderungen)</h4>
                            <ul style="padding-left: 20px; margin-top: 5px;">
                                <?php 
                                    // Annahme: Anforderungen sind durch Punkte oder Zeilenumbrüche getrennt
                                    $reqs = explode('.', htmlspecialchars($job['requirements']));
                                    foreach ($reqs as $req) {
                                        $req = trim($req);
                                        if (!empty($req)) {
                                            echo '<li>' . $req . '.</li>';
                                        }
                                    }
                                ?>
                            </ul>
                            
                            <h4>Wir bieten Ihnen</h4>
                            <ul style="padding-left: 20px; margin-top: 5px;">
                                <?php 
                                    // Annahme: Vorteile sind durch Punkte oder Zeilenumbrüche getrennt
                                    $bens = explode('.', htmlspecialchars($job['benefits']));
                                    foreach ($bens as $ben) {
                                        $ben = trim($ben);
                                        if (!empty($ben)) {
                                            echo '<li>' . $ben . '.</li>';
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        
                        <a href="mailto:karriere@ihre-klinik.de?subject=Bewerbung: <?= urlencode($job['title']) ?>" 
                           class="btn-primary" 
                           style="display: block; text-align: center; padding: 10px; border-radius: 6px; text-decoration: none;">
                            Jetzt Bewerben
                        </a>
                        </article>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>

    </section>

</main>

<?php require_once __DIR__ . '/footer.php'; ?>