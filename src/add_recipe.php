<?php
require_once '../config/db.php';  // Korrigierter Pfad zur Datenbankverbindung

// Rezept hinzufügen
if ($_POST) {
    $db = new Database();
    $conn = $db->getConnection();

    // Rezeptdaten aus dem Formular übernehmen
    $title = $_POST['title'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];

    // SQL-Abfrage vorbereiten und ausführen
    $stmt = $conn->prepare("INSERT INTO recipes (title, description, ingredients, instructions) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $description, $ingredients, $instructions]);

    echo "Rezept erfolgreich hinzugefügt!";
}
?>
