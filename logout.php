<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/header.php';
session_unset();
session_destroy();
session_start();
flash('success', 'Sie haben sich erfolgreich abgemeldet!');
header('Location: index.php');
exit;
