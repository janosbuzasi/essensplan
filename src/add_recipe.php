
<?php
require_once '../config/db.php';  // Verbindung zur Datenbank herstellen

if ($_POST) {
    $db = new Database();
    $conn = $db->getConnection();

    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];

    $stmt = $conn->prepare("INSERT INTO recipes (title, category, description, ingredients, instructions) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $category, $description, $ingredients, $instructions]);

    echo "Rezept erfolgreich hinzugefügt!";
}
?>
