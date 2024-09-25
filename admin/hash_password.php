<?php
// Überprüfen, ob das Formular abgeschickt wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Username und Passwort aus dem Formular übernehmen
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Passwort hashen
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Ausgabe des gehashten Passworts
    echo 'Username: ' . htmlspecialchars($username) . '<br>';
    echo 'Passwort: ' . htmlspecialchars($password) . '<br>';
    echo 'Gehashtes Passwort: ' . $hashedPassword . '<br>';
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Passwort Hash Generator</title>
</head>
<body>
    <h1>Passwort Hash Generator</h1>
    <form method="POST" action="">
        <label for="username">Benutzername:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Passwort:</label>
        <input type="password" id="password" name="password" required><br><br>
        
        <input type="submit" value="Generiere Hash">
    </form>
</body>
</html>
