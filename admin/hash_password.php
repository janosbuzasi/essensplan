<?php
// Beispielskript zum Hashen eines Passworts
$password = 'admin123'; // Setze hier dein gewünschtes Passwort
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo $hashedPassword;
?>
