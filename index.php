<?php require_once __DIR__ . '/header.php'; ?>


<section class="full-hero">
    <div class="hero-content container">
        <h1 class="hero-main-title">Mitfühlende Pflege.<br />Fortschrittliche Medizin.</h1>
        <p class="hero-main-subtitle">Erleben Sie erstklassige Gesundheitsversorgung mit Fachwissen und Mitgefühl an
            Ihrer Seite.</p>
    </div>
</section>

<?php
// Die erfolgreiche Abmelde-Nachricht ('Logged out') verwendet den 'success' Flash-Key.
if ($msg = flash('success')) {
    // HINZUGEFÜGTE KLASSE: 'auto-dismiss-toast' für JS-Targeting
    echo '<div class="toast visible auto-dismiss-toast" style="position:fixed;left:50%;transform:translateX(-50%);bottom:30px;z-index:9999;">' . htmlspecialchars($msg) . '</div>';
}
// Fehlermeldungen sollten im Allgemeinen länger bestehen bleiben, daher zielen wir nur auf die Erfolgsmeldung ab.
if ($err = flash('error')) {
    echo '<div class="toast visible" style="background:#c82333;position:fixed;left:50%;transform:translateX(-50%);bottom:30px;z-index:9999;">' . htmlspecialchars($err) . '</div>';
}
?>

<main class="container main-content">

    <section class="main-card">
        <div class="hero-grid">
            <div class="hero-left">
                <h2 class="hero-title">
                    Der <strong>Exzellenz</strong> in der Patientenversorgung verpflichtet
                </h2>
                <p class="hero-lead">
                    Unser engagiertes Team aus Ärzten, Pflegekräften und medizinischem
                    Fachpersonal bietet personalisierte Behandlungspläne mit der neuesten
                    Medizintechnik, um die besten Ergebnisse für jeden Patienten zu gewährleisten.
                </p>

                <div class="hero-cta">
                    <button class="btn btn-primary btn-book-appointment">
                        Jetzt Termin Buchen
                    </button>
                    <a class="btn btn-secondary" href="contact.html">Unsere Geschichte ansehen</a>
                </div>

                <div class="feature-strip">
                    <div class="feature">
                        <div class="feature-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-check">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>
                        </div>
                        <div class="feature-text">JCI Akkreditiert</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-star">
                                <polygon
                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        </div>
                        <div class="feature-text">Preisgekrönter Service</div>
                    </div>
                    <div class="feature">
                        <div class="feature-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-hand">
                                <path d="M18 11V5a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2" />
                                <path d="M14 7V5a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2" />
                                <path d="M10 11V5a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8" />
                                <path
                                    d="M18 11c0 2-2 3-2 3H8s-2-1-2-3c0-2.4 1.5-4 4-4 2.7 0 5 2 5 4h1a2 2 0 0 1 2 2v0c0 2-2 3-2 3H6" />
                            </svg>
                        </div>
                        <div class="feature-text">Vertrauenswürdige Pflege</div>
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div class="hero-image-frame">
                    <img src="images/img13.jpg" alt="Lächelnder männlicher Arzt in einem weißen Kittel"
                        onerror="this.onerror=null;this.src='https://placehold.co/560x400/999999/FFFFFF?text=Doctor+Portrait';" />
                </div>
            </div>
        </div>
    </section>

    <section class="secondary secondary-metrics">
        <h2 class="section-title">Exzellenz in Zahlen</h2>
        <div class="cards-grid metrics-grid">
            <div class="metric-card">
                <p class="metric-number">98.5%</p>
                <p class="metric-label">Patientenzufriedenheit</p>
            </div>

            <div class="metric-card">
                <p class="metric-number">5-Sterne</p>
                <p class="metric-label">Sicherheitsbewertung</p>
            </div>

            <div class="metric-card">
                <p class="metric-number">27+</p>
                <p class="metric-label">Jahre Erfahrung</p>
            </div>
        </div>
    </section>

    <section class="secondary">
        <h2 class="section-title">Der Alpha Unterschied: Unsere Kernwerte</h2>
        <p class="hero-lead section-subtitle">
            Wir bauen Vertrauen durch klinische Exzellenz, unerschütterliches Mitgefühl
            und ethische Praxis auf. Entdecken Sie die Werte, die jede unserer
            Entscheidungen leiten.
        </p>
        <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr))">
            <article class="info-card info-card-center">
                <div class="feature-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-heart">
                        <path
                            d="M19 14c1.49-1.46 3-3.23 3-5.55A5.55 5.55 0 0 0 16.5 3 5.55 5.55 0 0 0 12 5.09 5.55 5.55 0 0 0 7.5 3 5.55 5.55 0 0 0 2 8.45c0 2.32 1.5 4.09 3 5.55l7 7Z" />
                    </svg>
                </div>
                <h3>Mitgefühl Zuerst</h3>
                <p>
                    Wir behandeln jeden Patienten mit Empathie, Respekt und Würde und
                    stellen sicher, dass emotionaler Komfort neben der medizinischen
                    Behandlung Priorität hat.
                </p>
            </article>

            <article class="info-card info-card-center">
                <div class="feature-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-lamp">
                        <path d="M8 2h8l-1 6h-6l-1-6Z" />
                        <path d="m11 16 1-4 1 4 2-2 3 5H5l3-5 2 2Z" />
                        <path d="M12 22a4 4 0 0 0 4-4H8a4 4 0 0 0 4 4Z" />
                    </svg>
                </div>
                <h3>Klinische Innovation</h3>
                <p>
                    Wir engagieren uns für kontinuierliches Lernen und die rasche
                    Annahme bewährter, hochmoderner Medizintechnologien, um die
                    Patientenergebnisse zu verbessern.
                </p>
            </article>

            <article class="info-card info-card-center">
                <div class="feature-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-shield">
                        <path d="M20 13c0 5-6 9-8 9s-8-4-8-9V5l8-3 8 3z" />
                    </svg>
                </div>
                <h3>Sicherheit und Integrität</h3>
                <p>
                    Patientensicherheit ist nicht verhandelbar. Wir halten die höchsten
                    Standards professioneller und ethischer Integrität in allen
                    klinischen und administrativen Abläufen ein.
                </p>
            </article>
        </div>
    </section>

    <section class="secondary">
        <h2 class="section-title">Schwerpunkte und Fachwissen</h2>

        <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
            <article class="info-card">
                <img src="images/img18.jpg" alt="Wissenschaftler, die durch ein Mikroskop schauen" />
                <h3>Klinische Forschung & Studien</h3>
                <p>
                    Wir beteiligen uns aktiv an führenden nationalen und internationalen klinischen Studien. Dieses
                    Engagement für die Forschung stellt sicher, dass unsere Patienten frühen Zugang zu
                    vielversprechenden neuen Therapien und Behandlungen haben, bevor diese allgemein verfügbar sind.
                </p>
            </article>

            <article class="info-card">
                <img src="images/img19.jpg" alt="Eine Person meditiert oder macht leichte Übungen" />
                <h3>Präventive Wellness-Programme</h3>
                <p>
                    Mit Fokus auf proaktive Gesundheit umfassen unsere Wellness-Programme spezialisierte
                    Dienstleistungen für das Management chronischer Krankheiten, Ernährungsberatung und
                    Stressreduzierung, die Ihnen helfen, langfristige Gesundheit und Vitalität zu erhalten.
                </p>
            </article>

            <article class="info-card">
                <img src="images/img20.jpg" alt="Arzt spricht mit einem Patienten über einen Computerbildschirm" />
                <h3>Virtuelle Pflege & Telemedizin</h3>
                <p>
                    Erhalten Sie fachärztliche Beratungen bequem von zu Hause aus. Unsere sichere Telemedizin-Plattform
                    ermöglicht Folgetermine, Rezeptverwaltung und Spezialistenbesuche und macht die
                    Gesundheitsversorgung bequemer.
                </p>
            </article>
        </div>
    </section>

    <section class="secondary">
        <h2 class="section-title">Lernen Sie Unsere Vorgestellten Ärzte Kennen</h2>
        <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr))">
            <article class="info-card info-card-no-padding">
                <img src="images/img16.jpg" alt="Dr. Diego Rodriguez" style="
                        height: 300px;
                        object-fit: cover;
                        object-position: top center;
                    " />
                <div style="padding: 1.5rem; text-align: center">
                    <h3 style="margin-bottom: 0.25rem">Dr. Diego Rodriguez</h3>
                    <p style="
                            color: var(--color-primary);
                            font-weight: 600;
                            margin-bottom: 0;
                        ">
                        Chefarzt der Kardiologie
                    </p>
                </div>
            </article>
            <article class="info-card info-card-no-padding">
                <img src="images/img15.jpg" alt="Dr. Marcus Chen" style="
                        height: 300px;
                        object-fit: cover;
                        object-position: top center;
                    " />
                <div style="padding: 1.5rem; text-align: center">
                    <h3 style="margin-bottom: 0.25rem">Dr. Marcus Chen</h3>
                    <p style="
                            color: var(--color-primary);
                            font-weight: 600;
                            margin-bottom: 0;
                        ">
                        Direktor der Orthopädie
                    </p>
                </div>
            </article>
            <article class="info-card info-card-no-padding">
                <img src="images/img14.jpg" alt="Dr. Amelia Khan" style="
                        height: 300px;
                        object-fit: cover;
                        object-position: top center;
                    " />
                <div style="padding: 1.5rem; text-align: center">
                    <h3 style="margin-bottom: 0.25rem">Dr. Amelia Khan</h3>
                    <p style="
                            color: var(--color-primary);
                            font-weight: 600;
                            margin-bottom: 0;
                        ">
                        Pädiatrie-Spezialistin
                    </p>
                </div>
            </article>
        </div>
    </section>

    <section class="secondary">
        <h2 class="section-title">Was Unsere Patienten Sagen</h2>
        <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))">
            <article class="info-card testimonial-card">
                <p>
                    "Das Pflegeteam war aufmerksam, sachkundig und wahrhaft
                    mitfühlend. Sie haben eine beängstigende Erfahrung handhabbar
                    gemacht. Die Krankenschwestern fühlten sich wie Familie an. Ich
                    empfehle Alpha Hospital jedem, der fachkundige, personalisierte
                    Pflege sucht, wärmstens."
                </p>
                <p>— Sarah K., Chirurgische Patientin</p>
            </article>

            <article class="info-card testimonial-card">
                <p>
                    "Von der Diagnose bis zur Entlassung verlief die Kommunikation
                    reibungslos. Ich fühlte mich vollständig über meinen
                    Behandlungsplan informiert und die Ergebnisse waren besser, als ich
                    es mir erhoffen konnte. Ein wahrhaft erstklassiges medizinisches
                    Team."
                </p>
                <p>— David L., Kardiologischer Patient</p>
            </article>
        </div>
    </section>

    <section class="secondary">
        <h2 class="section-title">Unser Beitrag zur Gemeinschaft</h2>
        <div class="hero-grid" style="
                    grid-template-columns: 1fr 1.2fr;
                    gap: 3rem;
                    align-items: center;
                ">
            <div class="hero-image-frame" style="height: 400px">
                <img src="images/img17.jpg"
                    alt="Krankenhauspersonal leitet eine kostenlose Gesundheitsklinik in der Gemeinde"
                    style="object-fit: cover" />
            </div>

            <div class="hero-left" style="padding: 0">
                <h3 class="hero-title" style="font-size: 1.8rem; margin-bottom: 1rem">
                    Investition in eine Gesündere Zukunft
                </h3>
                <p class="hero-lead" style="margin-top: 0">
                    Wir sind bestrebt, über die Krankenhausmauern hinauszugehen. Unsere
                    Aufklärungsprogramme bieten kostenlose Gesundheits-Screenings,
                    Bildungs-Workshops und Wellness-Ressourcen für unterversorgte
                    Bevölkerungsgruppen in unserer Region.
                </p>
                <ul>
                    <li style="margin-bottom: 0.5rem; font-weight: 500">
                        Kostenlose Jährliche Grippeschutzimpf-Kliniken
                    </li>
                    <li style="margin-bottom: 0.5rem; font-weight: 500">
                        Ernährungs- und Diabetes-Management-Workshops
                    </li>
                    <li style="margin-bottom: 0.5rem; font-weight: 500">
                        Gesundheitsbildungsprogramme für Lokale Schulen
                    </li>
                </ul>
                <a class="btn btn-secondary" href="#" style="margin-top: 1rem">Erfahren Sie mehr über unsere
                    Stiftung</a>
            </div>
        </div>
    </section>

    <section class="secondary" style="
                    background: var(--bg-light);
                    padding-top: 5rem;
                    padding-bottom: 5rem;
                ">
        <h2 class="section-title">Gesundheitsressourcen & Aktuelle Nachrichten</h2>
        <p class="hero-lead section-subtitle">
            Bleiben Sie mit den neuesten Updates vom Alpha Hospital informiert,
            einschließlich Gesundheitstipps, Forschungsdurchbrüchen und
            Community-Veranstaltungen.
        </p>

        <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr))">
            <article class="info-card">
                <img src="images/img21.jpg" alt="Bild eines Laufschuhs" style="height: 200px" />
                <p style="
                        color: var(--color-text-medium);
                        font-size: 0.85rem;
                        margin-bottom: 0.5rem;
                    ">
                    10. MAI 2025 | WELLNESS
                </p>
                <h3>5 Tipps für den Start einer Gelenkschonenden Laufroutine</h3>
                <a class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem"
                    href="https://rehateam.cc/news/5-tipps-fuer-deinen-laufstart/" target="_blank">Artikel Lesen</a>
            </article>

            <article class="info-card">
                <img src="images/img22.jpg" alt="Bild von zwei arbeitenden Wissenschaftlern" style="height: 200px" />
                <p style="
                        color: var(--color-text-medium);
                        font-size: 0.85rem;
                        margin-bottom: 0.5rem;
                    ">
                    25. APRIL 2025 | FORSCHUNG
                </p>
                <h3>Durchbruch in der Nicht-Invasiven Krebs-Screening-Technologie</h3>
                <a class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem"
                    href="https://www.analytica-world.com/de/news/1179658/fingerabdruck-durchbruch-bei-der-erkennung-von-brustkrebs.html"
                    target="_blank">Artikel Lesen</a>
            </article>

            <article class="info-card">
                <img src="images/img23.jpg" alt="Bild eines Blutdruckmessgeräts" style="height: 200px" />
                <p style="
                        color: var(--color-text-medium);
                        font-size: 0.85rem;
                        margin-bottom: 0.5rem;
                    ">
                    1. APRIL 2025 | KARDIOLOGIE
                </p>
                <h3>Bluthochdruck Verstehen und Zuhause Managen</h3>
                <a class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.9rem"
                    href="https://www.heartfailurematters.org/de/das-koennen-sie-tun/selbstaendige-messung-von-blutdruck-und-puls/"
                    target="_blank">Artikel Lesen</a>
            </article>
        </div>
    </section>
</main>

// Formular zur Terminbuchung (Overlay)

<div id="appointmentOverlay" class="appointment-overlay">
    <div class="appointment-overlay-content">
        <button id="closeAppointment" class="close-btn" aria-label="Schließen">&times;</button>
        <h2>Termin Buchen</h2>
        <form id="apptForm" class="book-form" method="post" action="process_appointment.php" novalidate>
            <div class="form-row-group">
                <div class="form-row">
                    <label for="first-name">Vorname</label>
                    <input type="text" id="first-name" name="first_name" class="validate-me" placeholder="z.B. John"
                        required />
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <label for="last-name">Nachname</label>
                    <input type="text" id="last-name" name="last_name" class="validate-me" placeholder="z.B. Doe"
                        required />
                    <span class="error-msg"></span>
                </div>
            </div>
            <div class="form-row">
                <label for="email">E-Mail Adresse</label>
                <input type="email" id="email" name="email" class="validate-me" placeholder="john.doe@beispiel.de"
                    required />
                <span class="error-msg"></span>
            </div>
            <div class="form-row">
                <label for="phone">Telefonnummer</label>
                <input type="tel" id="phone" name="phone" class="validate-me" placeholder="(555) 123-4567" required />
                <span class="error-msg"></span>
            </div>
            <div class="form-row-group">
                <div class="form-row">
                    <label for="date-modal">Datum</label>
                    <input type="date" id="date-modal" name="appt_date" class="validate-me" required />
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <label for="time-modal">Bevorzugte Zeit</label>
                    <select id="time-modal" name="appt_time" class="validate-me" required>
                        <option value="">--- Zeit Auswählen ---</option>
                    </select>
                    <span class="error-msg"></span>
                </div>
            </div>
            <div class="form-row">
                <label for="department">Abteilung</label>
                <select id="department" name="department" class="validate-me" required>
                    <option value="">--- Abteilung Auswählen ---</option>
                    <option value="Emergency">Notaufnahme</option>
                    <option value="Pediatrics">Pädiatrie</option>
                    <option value="Surgery">Chirurgie</option>
                    <option value="Cardiology">Kardiologie</option>
                </select>
                <span class="error-msg"></span>
            </div>
            <div class="form-row">
                <label for="doctors">Ärzte</label>
                <select id="doctors" name="doctor" class="validate-me" required>
                    <option value="">--- Arzt Auswählen ---</option>
                    <option value="Dr. J. Muller">Dr. J. Müller</option>
                    <option value="Dr. M. Sana">Dr. M. Sana</option>
                    <option value="Dr. D. Lisa">Dr. D. Lisa</option>
                    <option value="Dr. I. Kana">Dr. I. Kana</option>
                </select>
                <span class="error-msg"></span>
            </div>
            <div class="form-row">
                <label for="message-modal">Grund des Besuchs</label>
                <textarea id="message-modal" name="reason" class="validate-me"
                    placeholder="Beschreiben Sie kurz Ihre Symptome oder den Grund des Besuchs..." required></textarea>
                <span class="error-msg"></span>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Jetzt Buchen</button>
            </div>
        </form>
    </div>
</div>

<div id="loginOverlay" class="appointment-overlay">
    <div class="appointment-overlay-content login-content">
        <button id="closeLogin" class="close-btn">&times;</button>

        <div id="loginView">
            <h2 style="text-align: center">Willkommen Zurück</h2>
            <form id="loginForm" class="book-form" action="process_login.php" method="POST" novalidate>
                <input type="hidden" name="action" value="login">

                <div class="form-row">
                    <label for="login-email">E-Mail Adresse</label>
                    <input type="email" id="login-email" name="login_email" class="validate-me"
                        placeholder="sie@beispiel.de" required />
                    <span class="error-msg"></span>
                </div>
                <div class="form-row">
                    <label for="login-password">Passwort</label>
                    <input type="password" id="login-password" name="login_password" class="validate-me"
                        placeholder="••••••••••" required />
                    <span class="error-msg"></span>
                </div>
                <div class="form-actions">
                    <button class="btn btn-primary" type="submit" style="width: 100%">Anmelden</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toastNotification" class="toast">Erfolg!</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Wählen Sie das Toast-Element mit der spezifischen Klasse aus, das für die automatische Schließung hinzugefügt wurde.
        const successToast = document.querySelector('.auto-dismiss-toast');

        if (successToast) {
            // Verzögerungszeit einstellen (1500 ms = 1,5 Sekunden)
            const delay = 1500;
            // Die CSS-Übergangszeit für 'toast' beträgt 0,35 Sekunden (350 ms).
            const transitionDuration = 350;

            setTimeout(() => {
                // Entferne die Klasse 'visible', um den in styles.css definierten Ausblendeffekt auszulösen.
                successToast.classList.remove('visible');

                // Warten Sie, bis der Ausblendvorgang abgeschlossen ist, 
                // und entfernen Sie dann das Element aus dem DOM.
                setTimeout(() => {
                    successToast.remove();
                }, transitionDuration);

            }, delay);
        }
    });
</script>
<?php require_once __DIR__ . '/footer.php'; ?>