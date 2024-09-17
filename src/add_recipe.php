<?php
require_once '../config/db.php';

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

<h1>Neues Rezept hinzufügen</h1>

<form method="POST" action="">
    <label for="title">Titel des Rezepts:</label>
    <input type="text" name="title" required>
    <br>

    <label for="category">Kategorie:</label>
    <select name="category">
        <option value="Vegetarisch">Vegetarisch</option>
        <option value="Vegan">Vegan</option>
        <option value="Fleischgericht">Fleischgericht</option>
    </select>
    <br>

    <label for="description">Beschreibung:</label>
    <textarea name="description" required></textarea>
    <br>

    <label for="ingredients">Zutaten:</label>
    <textarea name="ingredients" required></textarea>
    <br>

    <label for="instructions">Anleitung:</label>
    <textarea name="instructions" required></textarea>
    <br>

    <input type="submit" value="Rezept hinzufügen">
</form>
<a href="view_recipes.php">Zurück zu den Rezepten</a>
