<?php
// Beispielskript zum Hashen eines Passworts
$password = 'antonia'; // Setze hier dein gewünschtes Passwort
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo $hashedPassword;
?>
