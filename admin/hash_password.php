<?php
// Beispielskript zum Hashen eines Passworts
$password = 'antonia'; // Setze hier dein gewünschtes Passwort
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Ausgabe für den ersten Benutzer
echo 'Username: antonia, Passwort: ' . $password . ', Gehashtes Passwort: ' . $hashedPassword . '<br>';

// Neuer Benutzer
$password = 'admin'; // Setze hier dein gewünschtes Passwort
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Ausgabe für den zweiten Benutzer
echo 'Username: admin, Passwort: ' . $password . ', Gehashtes Passwort: ' . $hashedPassword;
?>
