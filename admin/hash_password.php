<?php
// Beispielskript zum Hashen eines Passworts
$password = 'admin'; // Setze hier dein gewünschtes Passwort
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo $hashedPassword;
?>
