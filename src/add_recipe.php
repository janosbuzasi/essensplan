<?php
$title = "Neues Rezept hinzufügen"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <form action="add_recipe.php" method="post">
        <label for="title">Titel des Rezepts:</label>
        <input type="text" name="title" required><br>
        <label for="ingredients">Zutaten:</label>
        <textarea name="ingredients" required></textarea><br>
        <label for="instructions">Zubereitung:</label>
        <textarea name="instructions" required></textarea><br>
        <label for="category">Kategorie:</label>
        <input type="text" name="category" required><br>
        <label for="prep_time">Vorbereitungszeit (Minuten):</label>
        <input type="number" name="prep_time" required><br>
        <label for="cook_time">Kochzeit (Minuten):</label>
        <input type="number" name="cook_time" required><br>
        <label for="difficulty">Schwierigkeitsgrad:</label>
        <select name="difficulty">
            <option value="leicht">Leicht</option>
            <option value="mittel">Mittel</option>
            <option value="schwer">Schwer</option>
        </select><br>
        <label for="servings">Portionen:</label>
        <input type="number" name="servings" required><br>
        <input type="submit" value="Rezept hinzufügen">
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO recipes (title, ingredients, instructions, category, prep_time, cook_time, difficulty, servings) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$_POST['title'], $_POST['ingredients'], $_POST['instructions'], $_POST['category'], $_POST['prep_time'], $_POST['cook_time'], $_POST['difficulty'], $_POST['servings']])) {
            echo "<p>Rezept erfolgreich hinzugefügt!</p>";
        } else {
            echo "<p>Fehler beim Hinzufügen des Rezepts.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
