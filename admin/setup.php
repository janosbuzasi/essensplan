<?php
// Error Reporting aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Passwortschutz
$valid_password = "deinSicheresPasswort";  // Setze hier dein gewünschtes Passwort

// Wenn kein Passwort eingegeben wurde oder das falsche Passwort übermittelt wurde
if (!isset($_POST['password']) || $_POST['password'] !== $valid_password) {
    // Einfaches HTML-Formular zur Passwortabfrage
    echo '<form method="POST">
            <h2>Admin Setup - Passwort erforderlich</h2>
            <label>Passwort:</label>
            <input type="password" name="password">
            <input type="submit" value="Login">
          </form>';
    exit;  // Stoppt die weitere Ausführung, bis das Passwort korrekt ist
}

// Wenn das Passwort korrekt ist, wird das Setup ausgeführt

// Datenbankkonfiguration
$host = 'localhost';
$dbname = 'wochenplan';
$username = '';  // Ersetze mit deinem DB-Benutzernamen
$password = '';      // Ersetze mit deinem DB-Passwort

try {
    // Verbindung zur MySQL-Datenbank aufbauen
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Datenbank erstellen, falls sie nicht existiert
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "Datenbank '$dbname' wurde erfolgreich erstellt oder existierte bereits.<br>";

    // Verbindung zur neu erstellten Datenbank herstellen
    $conn->exec("USE $dbname");

    // Tabelle für Rezepte erstellen, falls sie nicht existiert
    $sqlRecipes = "
        CREATE TABLE IF NOT EXISTS recipes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            ingredients TEXT,
            instructions TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlRecipes);
    echo "Tabelle 'recipes' wurde erfolgreich erstellt oder existierte bereits.<br>";

    // Tabelle für den Wochenplan erstellen, falls sie nicht existiert
    $sqlMealPlan = "
        CREATE TABLE IF NOT EXISTS meal_plan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipe_id INT,
            day_of_week ENUM('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
            FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlMealPlan);
    echo "Tabelle 'meal_plan' wurde erfolgreich erstellt oder existierte bereits.<br>";

    echo "<br>Setup erfolgreich abgeschlossen!<br>";

} catch (PDOException $e) {
    echo "Fehler bei der Datenbankeinrichtung: " . $e->getMessage();
}
?>
