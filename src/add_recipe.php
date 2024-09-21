<?php
$title = "Neues Rezept hinzufügen"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <form action="add_recipe.php" method="post">
        <label for="title">Titel:</label>
        <input type="text" name="title" required><br>
        <label for="ingredients">Zutaten:</label>
        <textarea name="ingredients" required></textarea><br>
        <label for="instructions">Anleitung:</label>
        <textarea name="instructions" required></textarea><br>
        <label for="category">Kategorie:</label>
        <select name="category">
            <?php
            require_once '../config/db.php';
            $db = new Database();
            $conn = $db->getConnection();

            // Abruf der Kategorien
            $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($categories as $category) {
                echo "<option value='" . $category['name'] . "'>" . $category['name'] . "</option>";
            }
            ?>
        </select><br>
        <label for="prep_time">Zubereitungszeit (Minuten):</label>
        <input type="number" name="prep_time"><br>
        <label for="cook_time">Kochzeit (Minuten):</label>
        <input type="number" name="cook_time"><br>
        <label for="difficulty">Schwierigkeitsgrad:</label>
        <select name="difficulty">
            <option value="leicht">Leicht</option>
            <option value="mittel">Mittel</option>
            <option value="schwer">Schwer</option>
        </select><br>
        <label for="servings">Portionen:</label>
        <input type="number" name="servings"><br>
        <input type="submit" value="Rezept hinzufügen">
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
