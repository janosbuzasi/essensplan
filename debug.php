<?php
// debug.php

// Einfache Fehlerbehandlung und Debugging aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Debugging für Essenspläne</h2>";

// Datenbankverbindung
require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

// Überprüfen, ob die Verbindung erfolgreich ist
if (!$conn) {
    die("Fehler: Datenbankverbindung fehlgeschlagen.");
} else {
    echo "<p>Datenbankverbindung erfolgreich hergestellt.</p>";
}

// Essenspläne abrufen
try {
    $stmt = $conn->query("SELECT * FROM essensplan ORDER BY year DESC, week_number DESC");
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Anzahl der abgerufenen Essenspläne anzeigen
    echo "<p>Anzahl der gefundenen Essenspläne: " . count($plans) . "</p>";

    // Details der abgerufenen Essenspläne anzeigen
    if ($plans) {
        echo "<h3>Details der gefundenen Essenspläne:</h3>";
        echo "<ul>";
        foreach ($plans as $plan) {
            echo "<li>Plan ID: " . $plan['id'] . " | Woche: " . $plan['week_number'] . " | Jahr: " . $plan['year'] . " | Beschreibung: " . htmlspecialchars($plan['description'] ?? '') . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Keine Essenspläne gefunden.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Fehler beim Abrufen der Essenspläne: " . $e->getMessage() . "</p>";
}

// Debugging-Informationen für Mahlzeitenkategorien abrufen
try {
    $stmt = $conn->query("SELECT * FROM meal_categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Anzahl der abgerufenen Mahlzeitenkategorien anzeigen
    echo "<p>Anzahl der gefundenen Mahlzeitenkategorien: " . count($categories) . "</p>";

    // Details der abgerufenen Mahlzeitenkategorien anzeigen
    if ($categories) {
        echo "<h3>Details der gefundenen Mahlzeitenkategorien:</h3>";
        echo "<ul>";
        foreach ($categories as $category) {
            echo "<li>Kategorie ID: " . $category['id'] . " | Name: " . htmlspecialchars($category['name']) . " | Beschreibung: " . htmlspecialchars($category['description'] ?? '') . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Keine Mahlzeitenkategorien gefunden.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Fehler beim Abrufen der Mahlzeitenkategorien: " . $e->getMessage() . "</p>";
}

// Debugging-Informationen für Rezepte abrufen
try {
    $stmt = $conn->query("SELECT * FROM recipes ORDER BY title ASC");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Anzahl der abgerufenen Rezepte anzeigen
    echo "<p>Anzahl der gefundenen Rezepte: " . count($recipes) . "</p>";

    // Details der abgerufenen Rezepte anzeigen
    if ($recipes) {
        echo "<h3>Details der gefundenen Rezepte:</h3>";
        echo "<ul>";
        foreach ($recipes as $recipe) {
            echo "<li>Rezept ID: " . $recipe['id'] . " | Titel: " . htmlspecialchars($recipe['title']) . " | Kategorie: " . htmlspecialchars($recipe['category'] ?? '') . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Keine Rezepte gefunden.</p>";
    }
} catch (PDOException $e) {
    echo "<p>Fehler beim Abrufen der Rezepte: " . $e->getMessage() . "</p>";
}

// Ende des Debugging-Skripts
echo "<p>Debugging abgeschlossen.</p>";

?>
