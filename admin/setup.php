
<?php
// Error Reporting aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Passwortschutz
$valid_password = "Passwort";  // Setze hier dein gewünschtes Passwort

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
$dbname = 'essensplan';  // Neuer Name der Datenbank
$username = 'essensplan';  // Ersetze mit deinem DB-Benutzernamen
$password = 'essensplan';      // Ersetze mit deinem DB-Passwort

try {
    // Verbindung zur MySQL-Datenbank aufbauen
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Datenbank erstellen, falls sie nicht existiert
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "Datenbank '$dbname' wurde erfolgreich erstellt oder existierte bereits.<br>";

    // Verbindung zur neu erstellten Datenbank herstellen
    $conn->exec("USE $dbname");

    // Tabelle für Essenspläne erstellen oder ändern, falls sie nicht existiert
    $sqlEssensplan = "
        CREATE TABLE IF NOT EXISTS essensplan (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            week_number INT(2) NOT NULL,
            year INT(4) NOT NULL,
            week_name VARCHAR(50),
            description TEXT,
            status VARCHAR(20) DEFAULT 'aktiv',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlEssensplan);
    echo "Tabelle 'essensplan' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Tabelle für Rezepte erstellen oder ändern, falls sie nicht existiert
    $sqlRecipes = "
        CREATE TABLE IF NOT EXISTS recipes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            ingredients TEXT NOT NULL,
            instructions TEXT NOT NULL,
            category VARCHAR(50) NOT NULL,
            prep_time INT(4),
            cook_time INT(4),
            difficulty VARCHAR(20),
            servings INT(4),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlRecipes);
    echo "Tabelle 'recipes' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Tabelle für Kategorien erstellen
    $sqlCategories = "
        CREATE TABLE IF NOT EXISTS categories (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlCategories);
    echo "Tabelle 'categories' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Tabelle für Zuordnung Essensplan zu Rezepten
    $sqlEssensplanRecipes = "
        CREATE TABLE IF NOT EXISTS essensplan_recipes (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            essensplan_id INT(11) NOT NULL,
            recipe_id INT(11) NOT NULL,
            day_of_week ENUM('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
            meal_type ENUM('Frühstück', 'Mittagessen', 'Abendessen'),
            FOREIGN KEY (essensplan_id) REFERENCES essensplan(id),
            FOREIGN KEY (recipe_id) REFERENCES recipes(id)
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlEssensplanRecipes);
    echo "Tabelle 'essensplan_recipes' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Standardwerte für Essenspläne hinzufügen
    $conn->exec("INSERT INTO essensplan (week_number, year, week_name, description, status) VALUES
        (1, 2024, 'Woche 1', 'Plan für die erste Woche des Jahres', 'aktiv'),
        (2, 2024, 'Woche 2', 'Plan für die zweite Woche des Jahres', 'aktiv')");
    echo "Standardwerte für 'essensplan' wurden hinzugefügt.<br>";

    // Standardwerte für Kategorien hinzufügen
    $conn->exec("INSERT INTO categories (name, description) VALUES
        ('Hauptgericht', 'Herzhafte Gerichte als Hauptmahlzeit'),
        ('Dessert', 'Süße Nachspeisen'),
        ('Vorspeise', 'Kleine Gerichte vor der Hauptspeise'),
        ('Getränke', 'Erfrischungen und Getränke')");
    echo "Standardwerte für 'categories' wurden hinzugefügt.<br>";

    // Standardwerte für Rezepte hinzufügen
    $conn->exec("INSERT INTO recipes (title, ingredients, instructions, category, prep_time, cook_time, difficulty, servings) VALUES
        ('Spaghetti Bolognese', 'Spaghetti, Tomaten, Hackfleisch, Zwiebeln, Knoblauch', '1. Hackfleisch anbraten. 2. Tomaten hinzufügen. 3. Spaghetti kochen. 4. Zusammen servieren.', 'Hauptgericht', 15, 30, 'mittel', 4),
        ('Panna Cotta', 'Sahne, Zucker, Gelatine, Vanille', '1. Sahne erhitzen. 2. Gelatine einrühren. 3. In Formen gießen und kühlen.', 'Dessert', 10, 120, 'leicht', 6)");
    echo "Standardwerte für 'recipes' wurden hinzugefügt.<br>";

    // Zuordnung von Rezepten zu Essensplänen hinzufügen
    $conn->exec("INSERT INTO essensplan_recipes (essensplan_id, recipe_id, day_of_week, meal_type) VALUES
        (1, 1, 'Montag', 'Mittagessen'),
        (1, 2, 'Dienstag', 'Dessert')");
    echo "Zuordnung von Rezepten zu Essensplänen wurde hinzugefügt.<br>";

    echo "<br>Setup erfolgreich abgeschlossen!<br>";

} catch (PDOException $e) {
    echo "Fehler bei der Datenbankeinrichtung: " . $e->getMessage();
}
?>
