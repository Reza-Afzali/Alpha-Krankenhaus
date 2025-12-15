<?php
// header.php
require_once __DIR__ . '/config.php'; // Stellt sicher, dass session_start() in dieser Datei enthalten ist
require_once __DIR__ . '/db.php';
?>
<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Alpha Hospital</title>
    <link rel="icon" type="image/png" href="images/favicon.png" />
    <link rel="stylesheet" href="styles.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
</head>

<body>
    <header class="header">
        <div class="container header-content">
            <a href="index.php" class="logo">
  <img src="images/logo.png" alt="Alpha Hospital Logo" class="logo-img">
  <span>Alpha Krankenhaus</span>
</a>

            <nav class="nav-menu" aria-label="Primäre Navigation">
                <a href="index.php" class="nav-link">Startseite</a>
                <a href="about.php" class="nav-link">Über Uns</a>
                <a href="services.php" class="nav-link">Leistungen</a>
                <a href="career.php" class="nav-link">Karriere</a>
                <a href="contact.php" class="nav-link">Kontakt</a>
            </nav>

            <div class="header-auth">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <div style="display:flex; gap:.5rem; align-items:center">
                        <a href="admin.php" style="text-decoration: none; display: flex; align-items: center;">
                        <span style="color:#fff; font-weight:600; margin-right:.5rem">
                            Hallo, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Benutzer'); ?>
                        </span></a>
                        <form method="post" action="logout.php" style="margin:0">
                            <button class="btn btn-secondary" type="submit">Abmelden</button>
                        </form>
                    </div>
                <?php else: ?>
                    <button id="headerLoginBtn" class="btn btn-primary cta-btn-desktop">Anmelden</button>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menü öffnen">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <div id="mobileNav" class="mobile-nav" aria-hidden="true">
            <div class="menu-links">
                <a href="index.php">Startseite</a>
                <a href="about.php">Über Uns</a>
                <a href="contact.php">Kontakt</a>
            </div>
        </div>
    </header>