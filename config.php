<?php
// config.php
session_start();

require_once __DIR__ . '/db.php';

// Flash-Nachrichten-Helfer
function flash($key, $value = null)
{
    if ($value === null) {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    } else {
        $_SESSION['flash'][$key] = $value;
    }
}
?>