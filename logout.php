<?php
session_start(); // Start der Session

// Überprüfen, ob der Benutzer eingeloggt ist
if (isset($_SESSION['username'])) {
    // Alle Session-Daten löschen
    $_SESSION = array();
    
    // Session-Cookie löschen
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Session zerstören
    session_destroy();
}

// Benutzer zurück zur Startseite leiten oder zur Anmeldeseite
header("Location: /essensplan/index.php");
exit;
