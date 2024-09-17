
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

// Datenbankkonfiguration
$host = 'localhost';
$dbname = 'wochenplan';
$username = 'root';  // Ersetze mit deinem DB-Benutzernamen
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

    // Tabelle für Rezepte erstellen oder ändern, falls sie nicht existiert
    $sqlRecipes = "
        CREATE TABLE IF NOT EXISTS recipes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(100),  /* Kategorie-Feld hinzugefügt */
            description TEXT,
            ingredients TEXT,
            instructions TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlRecipes);
    echo "Tabelle 'recipes' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Rezeptkategorien hinzufügen
    $conn->exec("ALTER TABLE recipes ADD COLUMN IF NOT EXISTS category VARCHAR(100);");
    echo "Rezeptkategorie wurde zur Tabelle 'recipes' hinzugefügt.<br>";

    // Tabelle für Wochenpläne erstellen, falls sie nicht existiert
    $sqlWeekPlan = "
        CREATE TABLE IF NOT EXISTS week_plan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            week_number INT NOT NULL,
            year INT NOT NULL,
            archived TINYINT(1) DEFAULT 0, /* Archivierungs-Feld hinzugefügt */
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlWeekPlan);
    echo "Tabelle 'week_plan' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Standardrezepte einfügen
    $conn->exec("
        INSERT INTO recipes (title, category, description, ingredients, instructions) VALUES
        ('Frühstück', 'Vegetarisch', 'Ein leckeres Frühstück.', 'Brot, Butter, Marmelade', 'Serviere das Frühstück.'),
        ('Mittagessen', 'Fleischgericht', 'Ein köstliches Mittagessen.', 'Reis, Hähnchen, Gemüse', 'Bereite das Mittagessen zu.'),
        ('Abendessen', 'Vegetarisch', 'Ein schmackhaftes Abendessen.', 'Kartoffeln, Fisch, Salat', 'Bereite das Abendessen vor.')
    ");
    echo "Standardrezepte wurden mit Kategorien hinzugefügt.<br>";

    // Standard-Wochenplan erstellen (z.B. Woche 1 im aktuellen Jahr)
    $currentYear = date('Y');
    $conn->exec("INSERT INTO week_plan (week_number, year) VALUES (1, $currentYear)");
    $weekPlanId = $conn->lastInsertId();
    echo "Standard-Wochenplan (Woche 1) wurde erstellt.<br>";

    // Mahlzeiten für Montag bis Sonntag hinzufügen
    $daysOfWeek = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
    $mealTypes = ['Frühstück', 'Mittagessen', 'Abendessen'];

    foreach ($daysOfWeek as $day) {
        foreach ($mealTypes as $index => $mealType) {
            $recipeId = $index + 1;  // Frühstück -> Rezept 1, Mittagessen -> Rezept 2, Abendessen -> Rezept 3
            $conn->exec("INSERT INTO meal_plan (week_plan_id, recipe_id, day_of_week, meal_type) VALUES ($weekPlanId, $recipeId, '$day', '$mealType')");
        }
    }
    echo "Mahlzeiten für Woche 1 wurden hinzugefügt.<br>";

    echo "<br>Setup erfolgreich abgeschlossen!<br>";

} catch (PDOException $e) {
    echo "Fehler bei der Datenbankeinrichtung: " . $e->getMessage();
}
?>
