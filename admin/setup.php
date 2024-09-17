
<?php
// Error Reporting aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Passwortschutz
$valid_password = "MeinPasswort";  // Setze hier dein gewünschtes Passwort

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

// Datenbankkonfiguration
$host = 'localhost';
$dbname = 'wochenplan';
$username = 'wochenplan';  // Ersetze mit deinem DB-Benutzernamen
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

    // Tabelle für Wochenpläne erstellen, falls sie nicht existiert
    $sqlWeekPlan = "
        CREATE TABLE IF NOT EXISTS week_plan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            week_number INT NOT NULL,
            year INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlWeekPlan);
    echo "Tabelle 'week_plan' wurde erfolgreich erstellt oder existierte bereits.<br>";

    // Tabelle für den Wochenplan mit Mahlzeiten erstellen, falls sie nicht existiert
    $sqlMealPlan = "
        CREATE TABLE IF NOT EXISTS meal_plan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            week_plan_id INT,
            recipe_id INT,
            day_of_week ENUM('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
            meal_type ENUM('Frühstück', 'Mittagessen', 'Abendessen'),
            FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
            FOREIGN KEY (week_plan_id) REFERENCES week_plan(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlMealPlan);
    echo "Tabelle 'meal_plan' wurde erfolgreich erstellt oder existierte bereits.<br>";

    echo "<br>Setup erfolgreich abgeschlossen!<br>";

} catch (PDOException $e) {
    echo "Fehler bei der Datenbankeinrichtung: " . $e->getMessage();
}
?>
