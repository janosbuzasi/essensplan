<?php
$title = "Neues Rezept hinzufügen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Kategorien abrufen, falls man die optionale Auswahl trotzdem anbieten möchte
    $stmt = $conn->query("SELECT * FROM meal_categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <form action="add_recipe.php" method="post">
        <label for="title">Titel des Rezepts:</label><br>
        <input type="text" name="title" required><br><br>

        <label for="ingredients">Zutaten:</label><br>
        <textarea name="ingredients" rows="4" required></textarea><br><br>

        <label for="instructions">Zubereitung:</label><br>
        <textarea name="instructions" rows="4" required></textarea><br><br>

        <!-- Optional: Kategorienauswahl (falls nötig) -->
        <?php if ($categories): ?>
            <label for="category">Kategorie (optional):</label><br>
            <select name="category">
                <option value="">Keine Kategorie</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select><br><br>
        <?php endif; ?>

        <label for="prep_time">Vorbereitungszeit (Minuten):</label><br>
        <input type="number" name="prep_time" required><br><br>

        <label for="cook_time">Kochzeit (Minuten):</label><br>
        <input type="number" name="cook_time" required><br><br>

        <label for="difficulty">Schwierigkeitsgrad:</label><br>
        <select name="difficulty" required>
            <option value="">Bitte wählen...</option>
            <option value="leicht">Leicht</option>
            <option value="mittel">Mittel</option>
            <option value="schwer">Schwer</option>
        </select><br><br>

        <label for="servings">Portionen:</label><br>
        <input type="number" name="servings" required><br><br>

        <input type="submit" value="Rezept hinzufügen">
    </form>

    <?php
    // Verarbeitung des Formulars
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $ingredients = $_POST['ingredients'];
        $instructions = $_POST['instructions'];
        $category = $_POST['category']; // Kann leer sein
        $prepTime = $_POST['prep_time'];
        $cookTime = $_POST['cook_time'];
        $difficulty = $_POST['difficulty'];
        $servings = $_POST['servings'];

        // Überprüfung, ob alle Pflichtfelder ausgefüllt sind
        if ($title && $ingredients && $instructions && $prepTime && $cookTime && $difficulty && $servings) {
            // Rezept in die Datenbank einfügen
            $stmt = $conn->prepare("INSERT INTO recipes (title, ingredients, instructions, category, prep_time, cook_time, difficulty, servings) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $ingredients, $instructions, $category, $prepTime, $cookTime, $difficulty, $servings])) {
                echo "<p>Rezept erfolgreich hinzugefügt!</p>";
            } else {
                echo "<p>Fehler beim Hinzufügen des Rezepts.</p>";
            }
        } else {
            echo "<p>Bitte fülle alle Pflichtfelder aus.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
