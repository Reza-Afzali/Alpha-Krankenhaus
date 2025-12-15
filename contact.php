<?php require_once __DIR__ . '/header.php'; ?>

<main class="container main-content">
    <section class="main-card">
        <div class="hero-grid" style="grid-template-columns: 1fr; gap: 2rem">
            <div class="hero-left">
                <h2 class="hero-title">Support und Allgemeine Anfragen</h2>
                <p class="hero-lead">
                    In Notfällen rufen Sie immer sofort **112**. Für allgemeine
                    Krankenhausanfragen, Terminvereinbarungen oder Fragen zur
                    Abrechnung nutzen Sie bitte die unten stehenden Informationen.
                </p>

                <div
                    class="cards-grid"
                    style="
                        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                        margin-top: 2rem;
                    "
                >
                    <article class="info-card">
                        <h3>Allgemeine Rufnummer</h3>
                        <p>
                            Rufen Sie unsere Hauptnummer für Weiterleitungen,
                            allgemeine Informationen und Aufnahme an.
                        </p>
                        <p class="info-details">
                            (555) 123-4567
                        </p>
                    </article>

                    <article class="info-card">
                        <h3>Rechnungsabteilung</h3>
                        <p>
                            Für Fragen zu Rechnungen, Versicherungen oder
                            Zahlungsplänen senden Sie uns bitte direkt eine E-Mail.
                        </p>
                        <p class="info-details">
                            <a
                                href="mailto:billing@alphahospital.com"
                                style="text-decoration: none; color: inherit;"
                                >abrechnung@alphahospital.com</a
                            >
                        </p>
                    </article>

                    <article class="info-card">
                        <h3>E-Mail Support</h3>
                        <p>Senden Sie nicht eilige Fragen an unser Verwaltungsteam.</p>
                        <p class="info-details">
                            <a
                                href="mailto:info@alphahospital.com" style="text-decoration: none; color: inherit;"
                                >info@alphahospital.com</a
                            >
                        </p>
                    </article>
                    <article class="info-card">
                        <h3>Adresse</h3>
                        <p>
                            Besuchen Sie unseren Hauptcampus für vereinbarte Termine
                            oder Dienstleistungen.
                        </p>
                        <p class="info-details">
                            Gesundheitsallee 123, Wellness-Stadt, 90210
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="secondary">
        <h2 class="section-title">Unser Standort</h2>
        <div
            style="
                height: 400px;
                border-radius: 12px;
                overflow: hidden;
                box-shadow: var(--card-shadow);
            "
        >
            <iframe
                src="https://maps.google.com/maps?q=123%20Health%20Ave,%20Wellness%20City&t=&z=13&ie=UTF8&iwloc=&output=embed"
                width="100%"
                height="100%"
                style="border: 0"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Karte mit dem Standort des Alpha Hospital in der Gesundheitsallee 123, Wellness-Stadt"
            >
            </iframe>
        </div>

        <div style="text-align: center; margin-top: 3rem">
            <h3 class="hero-title" style="font-size: 2rem; margin-bottom: 0.5rem">
                Bereit zum Kontakt?
            </h3>
            <p class="hero-lead">
                Für nicht eilige Anfragen nutzen Sie unser sicheres Kontaktformular.
            </p>
            <button
                class="btn btn-primary btn-book-appointment"
                style="font-size: 1.15rem; padding: 0.9rem 2rem"; onclick="document.getElementById('contactModal').classList.add('show');"
            >
                Senden Sie uns eine Sichere Nachricht
            </button>
        </div>
    </section>
</main>
    
<div id="contactModal" class="appointment-overlay">
    <div class="appointment-overlay-content">
        <button onclick="document.getElementById('contactModal').classList.remove('show');"
            class="close-btn">×</button>
        <h2>Senden Sie uns eine Nachricht</h2>
        <form method="post" action="process_contact.php" class="book-form" novalidate>
            <div class="form-row">
                <label for="c-name">Vollständiger Name</label>
                <input type="text" id="c-name" name="name" class="validate-me" placeholder="z.B: Joe Müller" required />
                <span class="error-msg"></span>
            </div>
            <div class="form-row">
                <label for="c-email">E-Mail</label>
                <input type="email" id="c-email" name="email" class="validate-me" placeholder="ihre-email@gmail.com" required />
                <span class="error-msg"></span>
            </div>
            <div class="form-row">
                <label for="c-subject">Betreff</label>
                <select id="c-subject" name="subject" class="validate-me" required>
                    <option value="">--- Betreff auswählen ---</option>
                    <option>Allgemeine Anfrage</option>
                    <option>Abrechnungsfrage</option>
                    <option>Feedback</option>
                    <option>Sonstiges</option>
                </select>
                <span class="error-msg"></span>
            </div>
            <div class="form-row">
                <label for="c-message">Nachricht</label>
                <textarea id="c-message" name="message" rows="4" class="validate-me" placeholder="schreiben Sie Ihre Nachricht..." required></textarea>
                <span class="error-msg"></span>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Nachricht Senden</button>
            </div>
        </form>
    </div>
</div>

<div id="loginOverlay" class="appointment-overlay">
    <div class="appointment-overlay-content login-content">
        <button id="closeLogin" class="close-btn" aria-label="Schließen">
            ×
        </button>

         <div id="loginView">
        <h2 style="text-align: center">Willkommen Zurück</h2>
        <form id="loginForm" class="book-form" action="process_login.php" method="POST" novalidate>
            <input type="hidden" name="action" value="login">
            
            <div class="form-row">
                <label for="login-email">E-Mail Adresse</label>
                <input type="email" id="login-email" name="login_email" class="validate-me" placeholder="sie@beispiel.de" required />
                <span class="error-msg"></span>
            </div>
            <div class="form-row">
                <label for="login-password">Passwort</label>
                <input type="password" id="login-password" name="login_password" class="validate-me" placeholder="••••••••••" required />
                <span class="error-msg"></span>
            </div>
            <div class="form-actions">
                <button class="btn btn-primary" type="submit" style="width: 100%">Anmelden</button>
            </div>
        </form>
    </div>


    </div>
</div>

<div id="toastNotification" class="toast">Aktion erfolgreich!</div>


<?php require_once __DIR__ . '/footer.php'; ?>