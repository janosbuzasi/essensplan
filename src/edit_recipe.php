<?php
$title = "Rezept bearbeiten";
require '../header.php'; // Header einfügen

require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recipe) {
        // Formular zum Bearbeiten des Rezepts
        if ($_POST) {
            // Aktualisiere das Rezept mit den neuen Daten
            $title = $_POST['title'];
            $category = $_POST['category'];
            $ingredients = $_POST['ingredients'];
            $instructions = $_POST['instructions'];

            $stmt = $conn->prepare("UPDATE recipes SET title = ?, category = ?, ingredients = ?, instructions = ? WHERE id = ?");
            $stmt->execute([$title, $category, $ingredients, $instructions, $recipe_id]);

            echo "<p>Das Rezept wurde erfolgreich aktualisiert.</p>";
        }

        ?>
        <main>
            <h2><?php echo $title; ?></h2>
            <form method="post">
                <label for="title">Titel des Rezepts:</label>
                <input type="text" name="title" value="<?php echo $recipe['title']; ?>" required>

                <label for="category">Kategorie:</label>
                <input type="text" name="category" value="<?php echo $recipe['category']; ?>" required>

                <label for="ingredients">Zutaten:</label>
                <textarea name="ingredients" required><?php echo $recipe['ingredients']; ?></textarea>

                <label for="instructions">Zubereitung:</label>
                <textarea name="instructions" required><?php echo $recipe['instructions']; ?></textarea>

                <input type="submit" value="Speichern" class="btn btn-add">
            </form>
        </main>
        <?php
    } else {
        echo "<p>Rezept nicht gefunden.</p>";
    }
} else {
    echo "<p>Keine ID angegeben.</p>";
}
include '../footer.php'; // Footer einfügen
?>
