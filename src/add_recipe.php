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

    // Bestehende Kategorien abrufen
    $stmt = $conn->query("SELECT * FROM meal_categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($categories) {
        // Formular zur Rezepterstellung anzeigen, wenn Kategorien vorhanden sind
        ?>
        <form action="add_recipe.php" method="post">
            <label for="title">Titel des Rezepts:</label><br>
            <input type="text" name="title" required><br><br>

            <label for="ingredients">Zutaten:</label><br>
            <textarea name="ingredients" rows="4" required></textarea><br><br>

            <label for="instructions">Zubereitung:</label><br>
            <textarea name="instructions" rows="4" required></textarea><br><br>

            <label for="category">Kategorie:</label><br>
            <select name="category" required>
                <option value="">Bitte wählen...</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['name']; ?>"><?php echo $category['name']; ?></option>
                <?php endforeach; ?>
            </select><br><br>

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
    } else {
        echo "<p>Keine Kategorien verfügbar. Bitte füge zuerst eine Kategorie hinzu.</p>";
        echo "<a href='add_category.php'>Neue Kategorie hinzufügen</a>";
    }

    // Verarbeitung des Formulars
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $ingredients = $_POST['ingredients'];
        $instructions = $_POST['instructions'];
        $category = $_POST['category'];
        $prepTime = $_POST['prep_time'];
        $cookTime = $_POST['cook_time'];
        $difficulty = $_POST['difficulty'];
        $servings = $_POST['servings'];

        // Überprüfung, ob alle Felder ausgefüllt sind
        if ($title && $ingredients && $instructions && $category && $prepTime && $cookTime && $difficulty && $servings) {
            // Rezept in die Datenbank einfügen
            $stmt = $conn->prepare("INSERT INTO recipes (title, ingredients, instructions, category, prep_time, cook_time, difficulty, servings) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $ingredients, $instructions, $category, $prepTime, $cookTime, $difficulty, $servings])) {
                echo "<p>Rezept erfolgreich hinzugefügt!</p>";
            } else {
                echo "<p>Fehler beim Hinzufügen des Rezepts.</p>";
            }
        } else {
            echo "<p>Bitte fülle alle Felder aus.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
