# AlphaHospital – Termin- und Benutzerverwaltung

## Überblick
**AlphaHospital** ist eine webbasierte Anwendung zur Verwaltung von Arztterminen sowie zur Benutzer- und Rollenverwaltung (Admin, Mitarbeiter/Staff, Ärzte). Patienten können Termine online buchen, während Mitarbeiter und Administratoren diese über geschützte Dashboards verwalten.

Die Anwendung basiert auf einem schlanken, wartbaren Technologie-Stack mit PHP, MySQL (PDO) und Vanilla JavaScript und legt besonderen Wert auf Sicherheit und klare Trennung von Frontend- und Backend-Logik.

---

## Technologie-Stack

**Backend**
- PHP 7.4 oder höher
- PDO für sichere Datenbankzugriffe

**Datenbank**
- MySQL / MariaDB

**Frontend**
- HTML5
- CSS3 (`styles.css`)
- Vanilla JavaScript (`script.js`)

**Sicherheit**
- Passwort-Hashing mit `password_hash()` und Verifikation mit `password_verify()`
- Serverseitige Validierung aller Eingaben

**Kommunikation**
- AJAX-Endpunkte (Fetch API)
- Z. B. `fetch_booked_slots.php`, `process_*.php`

---

## Voraussetzungen

Stellen Sie sicher, dass folgende Komponenten installiert und korrekt konfiguriert sind:

- Webserver (Apache oder Nginx)
- PHP 7.4+ inklusive PDO-Erweiterung
- MySQL- oder MariaDB-Datenbank

---

## Projektstruktur

```
/ERM/
├── images/                         # Ordner für Bilder und grafische Assets.
├── about.php                       # Seite mit allgemeinen Informationen (z.B. Über 
├── admin.php                       # Dashboard für Administratoren.
├── alphahospital.sql               # Datenbank-Schema (Dump)
├── career.php                      # Seite für Stellenangebote oder 
├── config.php                      # Allgemeine Konfigurationsdatei.
├── contact.php                     # Kontaktformular-Seite.
├── datenschutz.php                 # Seite mit den Datenschutzbestimmungen.
├── db.php                          # Datenbankverbindung mit PDO.
├── delete_record.php               # Skript zur serverseitigen Löschung von 
├── edit_form.php                   # Formular zum Bearbeiten von Daten.
├── edit_user.php                   # Formular/Logik zur Bearbeitung von Benutzerdaten.
├── email_functions.php             # Funktionen zum Senden von 
├── fetch_booked_slots.php          # AJAX-Endpunkt zur Prüfung der Terminverfügbarkeit.
├── footer.php                      # Gemeinsamer Footer-Bereich.
├── header.php                      # Gemeinsamer Header-Bereich.
├── impressum.php                   # Impressumsseite.
├── index.php                       # Startseite (Terminbuchung für Patienten).
├── logout.php                      # Skript zur Benutzerabmeldung.
├── process_appointment_status.php  # Verarbeitet Statusänderungen von Terminen (z.B. 
├── process_appointment.php         # Serverseitige Verarbeitung der Terminbuchung.
├── process_aut.php                 # Verarbeitet den Login- und Logout-Vorgang (Authentifizierung).
├── process_contact.php             # Verarbeitet das Kontaktformular.
├── process_delete.php              # Serverseitige Terminlöschung 
├── process_login.php               # Skript zur Verarbeitung des Login-Formulars.
├── process_register.php            # Verarbeitet die Benutzerregistrierung (AJAX).
├── process_update_user.php         # Skript zur Aktualisierung von Benutzerdaten.
├── process_update.php              # Serverseitige Terminbearbeitung (explizit im README genannt).
├── readme.md                       # Projekt-Dokumentation und Installationsanleitung.
├── script.js                       # Client-seitige Logik (Vanilla JavaScript) und AJAX
├── services.php                    # Seite, die die angebotenen Dienstleistungen
├── staff.php                       # Dashboard für Mitarbeiter (Staff).
├── styles.css                      # Zentrales Stylesheet (CSS3).
└── test.php
```

---

## Datenbankstruktur

Bitte erstellen Sie die folgenden Tabellen und tragen Sie Ihre Zugangsdaten in `db.php` ein.

### Tabelle: `users`
Speichert Benutzerkonten für Administratoren, Mitarbeiter und Ärzte.

**Felder (Beispiel):**
- `id` (INT, PK, AUTO_INCREMENT)
- `email` (VARCHAR, UNIQUE)
- `password_hash` (VARCHAR)
- `full_name` (VARCHAR)
- `role` (ENUM: admin, staff, doctor)
- `created_at` (TIMESTAMP)

---

### Tabelle: `appointments`
Speichert alle Terminbuchungen von Patienten.

**Felder (Beispiel):**
- `id` (INT, PK, AUTO_INCREMENT)
- `first_name` (VARCHAR)
- `email` (VARCHAR)
- `appt_date` (DATE)
- `appt_time` (TIME)
- `doctor` (VARCHAR)
- `status` (ENUM: pending, approved, cancelled)
- `created_at` (TIMESTAMP)

---

### Tabelle: `messages`
Speichert Patientenanfragen und Kontaktformulare.

**Felder (Beispiel):**
- `id` (INT, PK, AUTO_INCREMENT)
- `name` (VARCHAR)
- `email` (VARCHAR)
- `message` (TEXT)
- `created_at` (TIMESTAMP)

---

## Funktionsübersicht

### Frontend (Patienten)
- Terminbuchung über die Startseite
- Echtzeit-Prüfung verfügbarer Zeitfenster (AJAX)
- Formularvalidierung auf Client- und Server-Seite

---

### Authentifizierung
- Login für Mitarbeiter und Administratoren
- Verarbeitung über `process_aut.php`
- Rollenbasierte Weiterleitung nach erfolgreichem Login

---

### Dashboards

**Admin-Dashboard (`admin.php`)**
- Übersicht aller Termine
- Genehmigen, Bearbeiten und Löschen von Terminen
- Benutzerverwaltung (optional erweiterbar)

**Mitarbeiter-Dashboard (`staff.php`)**
- Anzeige und Verwaltung zugewiesener Termine
- Statusänderungen (z. B. genehmigt / abgelehnt)

---

## Serverseitige Verarbeitung

- Änderungen an Terminen erfolgen ausschließlich über serverseitige Skripte
- Beispiele:
  - `process_update.php`
  - `process_delete.php`
- Schutz vor SQL-Injection durch vorbereitete Statements (PDO)

---

## Installation

1. Projektverzeichnis in das Webserver-Root kopieren (z. B. `/var/www/html/alphahospital`)
2. Datenbank erstellen und Tabellen anlegen
3. Zugangsdaten in `db.php` eintragen
4. Sicherstellen, dass PHP-PDO aktiviert ist
5. Anwendung im Browser aufrufen

---

## Erweiterbarkeit

Das System ist modular aufgebaut und kann leicht erweitert werden, z. B. um:
- E-Mail-Benachrichtigungen
- Mehrsprachigkeit
- Rollen- und Rechteverwaltung
- Kalender-Integration

---

## Lizenz

Dieses Projekt ist als internes oder Lernprojekt konzipiert. Eine produktive Nutzung sollte erst nach zusätzlicher Sicherheits- und Codeprüfung erfolgen.

---

**AlphaHospital** – Saubere Struktur, sichere Terminverwaltung, moderne Webtechnologien.

