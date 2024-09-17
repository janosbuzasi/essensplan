<?php
require_once '../config/db.php';

if (!isset($_GET['recipe_id'])) {
    echo "Rezept-ID nicht angegeben.";
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$recipe_id = $_GET['recipe_id'];

// Rezept abrufen
$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_POST) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];

    $stmt = $conn->prepare("UPDATE recipes SET title = ?, category = ?, description = ?, ingredients = ?, instructions = ? WHERE id = ?");
    $stmt->execute([$title, $category, $description, $ingredients, $instructions, $recipe_id]);

    echo "Rezept erfolgreich aktualisiert!";
}
?>

<h1>Rezept bearbeiten</h1>

<form method="POST" action="">
    <label for="title">Titel des Rezepts:</label>
    <input type="text" name="title" value="<?php echo $recipe['title']; ?>" required>
    <br>

    <label for="category">Kategorie:</label>
    <select name="category">
        <option value="Vegetarisch" <?php if ($recipe['category'] == 'Vegetarisch') echo 'selected'; ?>>Vegetarisch</option>
        <option value="Vegan" <?php if ($recipe['category'] == 'Vegan') echo 'selected'; ?>>Vegan</option>
        <option value="Fleischgericht" <?php if ($recipe['category'] == 'Fleischgericht') echo 'selected'; ?>>Fleischgericht</option>
    </select>
    <br>

    <label for="description">Beschreibung:</label>
    <textarea name="description" required><?php echo $recipe['description']; ?></textarea>
    <br>

    <label for="ingredients">Zutaten:</label>
    <textarea name="ingredients" required><?php echo $recipe['ingredients']; ?></textarea>
    <br>

    <label for="instructions">Anleitung:</label>
    <textarea name="instructions" required><?php echo $recipe['instructions']; ?></textarea>
    <br>

    <input type="submit" value="Rezept aktualisieren">
</form>
<a href="view_recipes.php">ZurÃ¼ck zu den Rezepten</a>
