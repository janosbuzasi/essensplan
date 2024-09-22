<?php
// Error Reporting aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Datenbankverbindung herstellen
require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

try {
    // Tabelle für Essenspläne erstellen oder ändern, falls sie nicht existiert
    $sqlEssensplan = "
        CREATE TABLE IF NOT EXISTS essensplan (
            id INT AUTO_INCREMENT PRIMARY KEY,
            week_number INT NOT NULL,
            year INT NOT NULL,
            week_name VARCHAR(255),
            description TEXT,
            status ENUM('aktiv', 'archiviert') DEFAULT 'aktiv',
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

    // Tabelle für Mahlzeitenkategorien erstellen, falls sie nicht existiert
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

    // Tabelle für Essensplan-Rezepte erstellen oder ändern, falls sie nicht existiert
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
    ");
    echo "Standardkategorien wurden hinzugefügt.<br>";

    // Standardrezepte hinzufügen
    $conn->exec("
        INSERT INTO recipes (title, ingredients, instructions, category, prep_time, cook_time, difficulty, servings) VALUES
        ('Pancakes', 'Mehl, Milch, Eier, Zucker, Salz, Backpulver', 'Alle Zutaten vermischen und in einer Pfanne ausbacken.', 'Frühstück', 10, 15, 'leicht', 4),
        ('Salat mit Hähnchenbrust', 'Hähnchenbrust, Salat, Tomaten, Gurken, Olivenöl, Essig', 'Hähnchenbrust anbraten und auf den Salat geben.', 'Mittagessen', 15, 20, 'mittel', 2),
        ('Spaghetti Bolognese', 'Spaghetti, Hackfleisch, Tomaten, Zwiebeln, Knoblauch, Olivenöl', 'Hackfleisch anbraten, Zwiebeln und Knoblauch hinzufügen, mit Tomaten köcheln lassen.', 'Abendessen', 10, 30, 'leicht', 4)
    ");
    echo "Standardrezepte wurden hinzugefügt.<br>";

    // Beispiel-Wochenplan hinzufügen
    $conn->exec("INSERT INTO essensplan (week_number, year, week_name, description) VALUES (1, 2024, 'Woche 1', 'Beispielhafter Essensplan für Woche 1')");
    $weekPlanId = $conn->lastInsertId();
    echo "Beispiel-Wochenplan wurde erstellt.<br>";

    // Mahlzeiten für Montag bis Sonntag hinzufügen
    $daysOfWeek = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
    $mealCategories = $conn->query("SELECT * FROM meal_categories")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($daysOfWeek as $day) {
        foreach ($mealCategories as $index => $category) {
            $recipeId = ($index % 3) + 1; // Weist die ersten drei Rezepte zu (Pancakes, Salat, Spaghetti)
            $conn->exec("INSERT INTO essensplan_recipes (essensplan_id, recipe_id, day_of_week, meal_category_id) VALUES ($weekPlanId, $recipeId, '$day', " . $category['id'] . ")");
        }
    }
    echo "Beispielmahlzeiten für Woche 1 wurden hinzugefügt.<br>";

    echo "<br>Setup erfolgreich abgeschlossen!<br>";

} catch (PDOException $e) {
    echo "Fehler bei der Datenbankeinrichtung: " . $e->getMessage();
}
?>
