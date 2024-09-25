<?php
// Beispielskript zum Hashen eines Passworts
$password = 'antonia'; // Setze hier dein gewünschtes Passwort
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Ausgabe für den ersten Benutzer
echo 'Username: antonia, Gehashtes Passwort: ' . $hashedPassword . '<br>';

// Neuer Benutzer
$password = 'admin'; // Setze hier dein gewünschtes Passwort
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Ausgabe für den zweiten Benutzer
echo 'Username: admin, Gehashtes Passwort: ' . $hashedPassword;
?>
