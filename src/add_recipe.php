<?php
$title = "Neues Rezept hinzufügen";
require '../header.php'; // Header einfügen

require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

$error = ""; // Variable für Fehlermeldungen

// Vorhandene Kategorien abrufen, um sie im Dropdown anzuzeigen
$stmt = $conn->query("SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_POST) {
    // Rezeptdaten aus dem Formular abrufen
    $title = $_POST['title'];
    $category = !empty($_POST['custom_category']) ? $_POST['custom_category'] : $_POST['category'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $prep_time = $_POST['prep_time'];
    $cook_time = $_POST['cook_time'];
    $difficulty = $_POST['difficulty'];
    $servings = $_POST['servings'];

    // Validierung der Felder für Vorbereitungszeit, Kochzeit, Portionen und Schwierigkeitsgrad
    $allowed_difficulties = ["leicht", "mittel", "schwer"]; // Erlaubte Werte für Schwierigkeitsgrad

    if (!is_numeric($prep_time) || $prep_time <= 0) {
        $error .= "Bitte eine gültige Vorbereitungszeit (größer als 0) angeben.<br>";
    }

    if (!is_numeric($cook_time) || $cook_time <= 0) {
        $error .= "Bitte eine gültige Kochzeit (größer als 0) angeben.<br>";
    }

    if (!is_numeric($servings) || $servings <= 0) {
        $error .= "Bitte eine gültige Anzahl von Portionen (größer als 0) angeben.<br>";
    }

    // Validierung des Schwierigkeitsgrads
    if (!in_array($difficulty, $allowed_difficulties)) {
        $error .= "Bitte einen gültigen Schwierigkeitsgrad wählen: leicht, mittel oder schwer.<br>";
    }

    // Wenn kein Fehler aufgetreten ist, Rezept in die Datenbank einfügen
    if (empty($error)) {
        try {
            $stmt = $conn->prepare("INSERT INTO recipes (title, category, ingredients, instructions, prep_time, cook_time, difficulty, servings) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $category, $ingredients, $instructions, $prep_time, $cook_time, $difficulty, $servings]);
            echo "<p>Das Rezept wurde erfolgreich hinzugefügt.</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Fehler beim Einfügen in die Datenbank: " . $e->getMessage() . "</p>";
        }
    } else {
        // Fehlermeldungen anzeigen
        echo "<p style='color:red;'>$error</p>";
    }
}
?>

<main>
    <h2><?php echo $title; ?></h2>
    <form method="post" class="recipe-form"> <!-- CSS-Klasse für das Formular -->
        <div class="form-group"> <!-- Container für jedes Feld und Label -->
            <label for="title">Titel des Rezepts:</label>
            <input type="text" name="title" required>
        </div>

        <div class="form-group">
            <label for="category">Mahlzeitenkategorie (optional):</label>
            <select name="category">
                <option value="">Mahlzeitenkategorie wählen</option>
                <?php
                foreach ($categories as $cat) {
                    echo "<option value='" . htmlspecialchars($cat['category'], ENT_QUOTES) . "'>" . htmlspecialchars($cat['category'], ENT_QUOTES) . "</option>";
                }
                ?>
                <option value="custom">Andere (bitte unten eintragen)</option>
            </select>
            <input type="text" name="custom_category" placeholder="Andere Kategorie">
        </div>

        <div class="form-group">
            <label for="ingredients">Zutaten:</label>
            <textarea name="ingredients" required></textarea>
        </div>

        <div class="form-group">
            <label for="instructions">Zubereitung:</label>
            <textarea name="instructions" required></textarea>
        </div>

        <div class="form-group">
            <label for="prep_time">Vorbereitungszeit (Minuten):</label>
            <input type="number" name="prep_time" min="1" required>
        </div>

        <div class="form-group">
            <label for="cook_time">Kochzeit (Minuten):</label>
            <input type="number" name="cook_time" min="1" required>
        </div>

        <div class="form-group">
            <label for="difficulty">Schwierigkeitsgrad:</label>
            <select name="difficulty">
                <option value="leicht">Leicht</option>
                <option value="mittel">Mittel</option>
                <option value="schwer">Schwer</option>
            </select>
        </div>

        <div class="form-group">
            <label for="servings">Portionen:</label>
            <input type="number" name="servings" min="1" required>
        </div>

        <input type="submit" value="Rezept hinzufügen" class="btn btn-add">
    </form>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
