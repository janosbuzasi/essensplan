
<?php
require_once '../config/db.php';

try {
    // Verbindung zur Datenbank herstellen
    $db = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Tabelle für Essenspläne erstellen
    $sql = "CREATE TABLE IF NOT EXISTS essensplan (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        week_number INT(2) NOT NULL,
        year INT(4) NOT NULL,
        week_name VARCHAR(50),
        description TEXT,
        status VARCHAR(20) DEFAULT 'aktiv',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($sql);

    // Tabelle für Rezepte erstellen
    $sql = "CREATE TABLE IF NOT EXISTS recipes (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        ingredients TEXT NOT NULL,
        instructions TEXT NOT NULL,
        category VARCHAR(50) NOT NULL,
        prep_time INT(4),
        cook_time INT(4),
        difficulty VARCHAR(20),
        servings INT(4),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($sql);

    // Tabelle für Kategorien erstellen
    $sql = "CREATE TABLE IF NOT EXISTS categories (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT
    )";
    $db->exec($sql);

    // Tabelle für Zuordnung Essensplan zu Rezepten
    $sql = "CREATE TABLE IF NOT EXISTS essensplan_recipes (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        essensplan_id INT(11) NOT NULL,
        recipe_id INT(11) NOT NULL,
        day_of_week ENUM('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
        meal_type ENUM('Frühstück', 'Mittagessen', 'Abendessen'),
        FOREIGN KEY (essensplan_id) REFERENCES essensplan(id),
        FOREIGN KEY (recipe_id) REFERENCES recipes(id)
    )";
    $db->exec($sql);

    // Standardwerte für Essenspläne
    $db->exec("INSERT INTO essensplan (week_number, year, week_name, description, status) VALUES
        (1, 2024, 'Woche 1', 'Plan für die erste Woche des Jahres', 'aktiv'),
        (2, 2024, 'Woche 2', 'Plan für die zweite Woche des Jahres', 'aktiv')");

    // Standardwerte für Kategorien
    $db->exec("INSERT INTO categories (name, description) VALUES
        ('Hauptgericht', 'Herzhafte Gerichte als Hauptmahlzeit'),
        ('Dessert', 'Süße Nachspeisen'),
        ('Vorspeise', 'Kleine Gerichte vor der Hauptspeise'),
        ('Getränke', 'Erfrischungen und Getränke')");

    // Standardwerte für Rezepte
    $db->exec("INSERT INTO recipes (title, ingredients, instructions, category, prep_time, cook_time, difficulty, servings) VALUES
        ('Spaghetti Bolognese', 'Spaghetti, Tomaten, Hackfleisch, Zwiebeln, Knoblauch', '1. Hackfleisch anbraten. 2. Tomaten hinzufügen. 3. Spaghetti kochen. 4. Zusammen servieren.', 'Hauptgericht', 15, 30, 'mittel', 4),
        ('Panna Cotta', 'Sahne, Zucker, Gelatine, Vanille', '1. Sahne erhitzen. 2. Gelatine einrühren. 3. In Formen gießen und kühlen.', 'Dessert', 10, 120, 'leicht', 6)");

    // Zuordnung von Rezepten zu Essensplänen mit validen 'meal_type' Werten
    $db->exec("INSERT INTO essensplan_recipes (essensplan_id, recipe_id, day_of_week, meal_type) VALUES
        (1, 1, 'Montag', 'Mittagessen'),
        (1, 2, 'Dienstag', 'Abendessen')"); // 'Dessert' wurde auf 'Abendessen' geändert

    echo "Datenbank-Setup erfolgreich abgeschlossen!";
} catch (PDOException $e) {
    echo "Fehler bei der Datenbankeinrichtung: " . $e->getMessage();
}
?>
