<?php
// Error Reporting aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung herstellen
require_once '../config/db.php'; // Korrigierter Pfad zur Datenbankkonfiguration
$db = new Database();
$conn = $db->getConnection();

// Überprüfen, ob die Datenbank und Tabellen bereits existieren
$databaseExists = false;
try {
    $result = $conn->query("SHOW TABLES LIKE 'essensplan'");
    if ($result->rowCount() > 0) {
        $databaseExists = true;
    }
} catch (PDOException $e) {
    echo "Fehler beim Überprüfen der Datenbank: " . $e->getMessage();
    exit;
}

// Wenn die Datenbank existiert, nach Bestätigung fragen
if ($databaseExists) {
    echo '<h2>Datenbank "essensplan" existiert bereits!</h2>';
    echo '<p>Möchtest du alle bestehenden Tabellen löschen und das Setup neu starten?</p>';
    echo '<form method="POST">
            <input type="hidden" name="confirm_reset" value="yes">
            <button type="submit">Ja, alle Tabellen löschen und neu erstellen</button>
          </form>';
    echo '<form method="POST">
            <input type="hidden" name="confirm_reset" value="no">
            <button type="submit">Nein, Setup abbrechen</button>
          </form>';

    // Verarbeitung der Benutzerentscheidung
    if (isset($_POST['confirm_reset'])) {
        if ($_POST['confirm_reset'] === 'no') {
            echo 'Setup wurde abgebrochen.';
            exit;
        } elseif ($_POST['confirm_reset'] === 'yes') {
            // Passwortabfrage
            if (!isset($_POST['password'])) {
                echo '<form method="POST">
                        <input type="hidden" name="confirm_reset" value="yes">
                        <label>Bitte Passwort eingeben:</label>
                        <input type="password" name="password">
                        <button type="submit">Bestätigen</button>
                      </form>';
                exit; // Abbruch bis Passwort eingegeben wird
            }

            // Überprüfen, ob das richtige Passwort eingegeben wurde
            if ($_POST['password'] !== 'essensplan') {
                echo 'Falsches Passwort. Setup wurde abgebrochen.';
                exit;
            }

            try {
                // Löschen der bestehenden Tabellen
                $conn->exec("DROP TABLE IF EXISTS essensplan_recipes, recipes, meal_categories, essensplan, users");
                echo "Alle bestehenden Tabellen wurden gelöscht.<br>";
            } catch (PDOException $e) {
                echo "Fehler beim Löschen der Tabellen: " . $e->getMessage();
                exit;
            }
        }
    } else {
        exit; // Abbruch, wenn keine Auswahl getroffen wurde
    }
}

try {
    // Tabelle für Benutzer erstellen
    $sqlUsers = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            role ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlUsers);
    echo "Tabelle 'users' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Admin-Benutzer hinzufügen
    $adminPassword = password_hash('adminpasswort', PASSWORD_DEFAULT); // Passwort verschlüsseln
    $conn->exec("
        INSERT INTO users (username, password, email, role) VALUES
        ('admin', '$adminPassword', 'admin@example.com', 'admin')
        ON DUPLICATE KEY UPDATE username = 'admin';
    ");
    echo "Admin-Benutzer wurde hinzugefügt oder aktualisiert.<br>";

    // Tabelle für Essenspläne erstellen
    $sqlEssensplan = "
        CREATE TABLE IF NOT EXISTS essensplan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            week_number INT NOT NULL,
            year INT NOT NULL,
            description TEXT,
            status ENUM('aktiv', 'archiviert') DEFAULT 'aktiv',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlEssensplan);
    echo "Tabelle 'essensplan' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Tabelle für Rezepte erstellen
    $sqlRecipes = "
        CREATE TABLE IF NOT EXISTS recipes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            ingredients TEXT,
            instructions TEXT,
            category VARCHAR(100),
            prep_time INT,
            cook_time INT,
            difficulty ENUM('leicht', 'mittel', 'schwer'),
            servings INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlRecipes);
    echo "Tabelle 'recipes' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Tabelle für Mahlzeitenkategorien erstellen
    $sqlMealCategories = "
        CREATE TABLE IF NOT EXISTS meal_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlMealCategories);
    echo "Tabelle 'meal_categories' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Tabelle für Essensplan-Rezepte erstellen
    $sqlEssensplanRecipes = "
        CREATE TABLE IF NOT EXISTS essensplan_recipes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            essensplan_id INT NOT NULL,
            recipe_id INT NOT NULL,
            day_of_week ENUM('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
            meal_category_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (essensplan_id) REFERENCES essensplan(id) ON DELETE CASCADE,
            FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
            FOREIGN KEY (meal_category_id) REFERENCES meal_categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB;
    ";
    $conn->exec($sqlEssensplanRecipes);
    echo "Tabelle 'essensplan_recipes' wurde erfolgreich erstellt oder aktualisiert.<br>";

    // Standardkategorien hinzufügen
    $conn->exec("
        INSERT INTO meal_categories (name, description) VALUES
        ('Frühstück', 'Morgendliche Mahlzeit'),
        ('Znüni', 'Zwischenmahlzeit um 9 Uhr'),
        ('Mittagessen', 'Hauptmahlzeit am Mittag'),
        ('Zvieri', 'Zwischenmahlzeit um 16 Uhr'),
        ('Abendessen', 'Abendliche Mahlzeit')
        ON DUPLICATE KEY UPDATE name = VALUES(name);
    ");
    echo "Standardkategorien wurden hinzugefügt oder aktualisiert.<br>";

    // Standardrezepte hinzufügen
    $conn->exec("
        INSERT INTO recipes (title, ingredients, instructions, category, prep_time, cook_time, difficulty, servings) VALUES
        ('Pancakes', 'Mehl, Milch, Eier, Zucker, Salz, Backpulver', 'Alle Zutaten vermischen und in einer Pfanne ausbacken.', 'Frühstück', 10, 15, 'leicht', 4),
        ('Obstsalat', 'Äpfel, Bananen, Orangen, Honig', 'Alles in Stücke schneiden und vermischen.', 'Znüni', 5, 0, 'leicht', 2),
        ('Spaghetti Bolognese', 'Spaghetti, Hackfleisch, Tomaten, Zwiebeln, Knoblauch, Olivenöl', 'Hackfleisch anbraten, Zwiebeln und Knoblauch hinzufügen, mit Tomaten köcheln lassen.', 'Mittagessen', 10, 30, 'leicht', 4),
        ('Käsebrot', 'Brot, Butter, Käse, Gurkenscheiben', 'Brot mit Butter bestreichen, Käse und Gurkenscheiben belegen.', 'Zvieri', 5, 0, 'leicht', 1),
        ('Hähnchenbrust mit Gemüse', 'Hähnchenbrust, Brokkoli, Karotten, Olivenöl, Salz, Pfeffer', 'Hähnchenbrust braten und mit gedünstetem Gemüse servieren.', 'Abendessen', 15, 20, 'mittel', 2)
        ON DUPLICATE KEY UPDATE title = VALUES(title);
    ");
    echo "Standardrezepte wurden hinzugefügt oder aktualisiert.<br>";

    // Beispiel-Wochenplan hinzufügen
    $conn->exec("INSERT INTO essensplan (week_number, year, description) VALUES (1, 2024, 'Beispielhafter Essensplan für Woche 1')");
    $weekPlanId = $conn->lastInsertId();
    echo "Beispiel-Wochenplan wurde erstellt.<br>";

    // Mahlzeiten für Montag bis Sonntag hinzufügen
    $daysOfWeek = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
    $mealCategories = $conn->query("SELECT * FROM meal_categories")->fetchAll(PDO::FETCH_ASSOC);
    $recipes = $conn->query("SELECT * FROM recipes")->fetchAll(PDO::FETCH_ASSOC);

    // Verteilt die Rezepte auf die Mahlzeitenkategorien und Wochentage
    foreach ($daysOfWeek as $dayIndex => $day) {
        foreach ($mealCategories as $categoryIndex => $category) {
            $recipeId = $recipes[($dayIndex + $categoryIndex) % count($recipes)]['id']; // Gleichmäßige Verteilung der Rezepte
            $conn->exec("INSERT INTO essensplan_recipes (essensplan_id, recipe_id, day_of_week, meal_category_id) VALUES ($weekPlanId, $recipeId, '$day', " . $category['id'] . ")");
        }
    }
    echo "Mahlzeiten für Woche 1 wurden hinzugefügt.<br>";

    echo "<br>Setup erfolgreich abgeschlossen!<br>";

} catch (PDOException $e) {
    echo "Fehler bei der Datenbankeinrichtung: " . $e->getMessage();
}
?>
