<?php
$title = "Rezept bearbeiten";
require_once 'auth.php'; // Überprüfung der Benutzeranmeldung (auth.php einbinden)
require '../header.php'; // Header einfügen
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

$error = ""; // Variable für Fehlermeldungen
$recipe = null; // Variable für Rezeptdaten

// Vorhandene Kategorien abrufen, um sie im Dropdown anzuzeigen
$stmt = $conn->query("SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Überprüfen, ob eine Rezept-ID übergeben wurde
if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    // Rezeptdaten abrufen
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recipe) {
        // Formularverarbeitung
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

            // Wenn kein Fehler aufgetreten ist, Rezept in der Datenbank aktualisieren
            if (empty($error)) {
                try {
                    $stmt = $conn->prepare("UPDATE recipes SET title = ?, category = ?, ingredients = ?, instructions = ?, prep_time = ?, cook_time = ?, difficulty = ?, servings = ? WHERE id = ?");
                    $stmt->execute([$title, $category, $ingredients, $instructions, $prep_time, $cook_time, $difficulty, $servings, $recipe_id]);
                    echo "<p style='color: green;'>Das Rezept wurde erfolgreich aktualisiert.</p>";
                    // Rezeptdaten erneut abrufen, um das aktualisierte Rezept anzuzeigen
                    $stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
                    $stmt->execute([$recipe_id]);
                    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "<p style='color:red;'>Fehler beim Aktualisieren des Rezepts: " . $e->getMessage() . "</p>";
                }
            } else {
                // Fehlermeldungen anzeigen
                echo "<p style='color:red;'>$error</p>";
            }
        }
    } else {
        echo "<p>Rezept nicht gefunden.</p>";
    }
} else {
    echo "<p>Keine Rezept-ID angegeben.</p>";
}
?>

<main>
    <h2><?php echo $title; ?></h2>
    <?php if ($recipe): ?>
    <form method="post" class="recipe-form"> <!-- CSS-Klasse für das Formular -->
        <div class="form-group"> <!-- Container für jedes Feld und Label -->
            <label for="title">Titel des Rezepts:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($recipe['title'], ENT_QUOTES); ?>" required>
        </div>

        <div class="form-group">
            <label for="category">Mahlzeitenkategorie (optional):</label>
            <select name="category">
                <option value="">Mahlzeitenkategorie wählen</option>
                <?php
                foreach ($categories as $cat) {
                    $selected = ($cat['category'] == $recipe['category']) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($cat['category'], ENT_QUOTES) . "' $selected>" . htmlspecialchars($cat['category'], ENT_QUOTES) . "</option>";
                }
                ?>
                <option value="custom">Andere (bitte unten eintragen)</option>
            </select>
            <input type="text" name="custom_category" placeholder="Andere Kategorie">
        </div>

        <div class="form-group">
            <label for="ingredients">Zutaten:</label>
            <textarea name="ingredients" required><?php echo htmlspecialchars($recipe['ingredients'], ENT_QUOTES); ?></textarea>
        </div>

        <div class="form-group">
            <label for="instructions">Zubereitung:</label>
            <textarea name="instructions" required><?php echo htmlspecialchars($recipe['instructions'], ENT_QUOTES); ?></textarea>
        </div>

        <div class="form-group">
            <label for="prep_time">Vorbereitungszeit (Minuten):</label>
            <input type="number" name="prep_time" value="<?php echo htmlspecialchars($recipe['prep_time'], ENT_QUOTES); ?>" min="1" required>
        </div>

        <div class="form-group">
            <label for="cook_time">Kochzeit (Minuten):</label>
            <input type="number" name="cook_time" value="<?php echo htmlspecialchars($recipe['cook_time'], ENT_QUOTES); ?>" min="1" required>
        </div>

        <div class="form-group">
            <label for="difficulty">Schwierigkeitsgrad:</label>
            <select name="difficulty">
                <option value="leicht" <?php if ($recipe['difficulty'] == "leicht") echo "selected"; ?>>Leicht</option>
                <option value="mittel" <?php if ($recipe['difficulty'] == "mittel") echo "selected"; ?>>Mittel</option>
                <option value="schwer" <?php if ($recipe['difficulty'] == "schwer") echo "selected"; ?>>Schwer</option>
            </select>
        </div>

        <div class="form-group">
            <label for="servings">Portionen:</label>
            <input type="number" name="servings" value="<?php echo htmlspecialchars($recipe['servings'], ENT_QUOTES); ?>" min="1" required>
        </div>

        <!-- Buttons: Rezept aktualisieren und Formular zurücksetzen -->
        <div class="form-group">
            <button type="submit" class="btn btn-edit" title="Rezept aktualisieren">
                <i class="fas fa-save"></i> Speichern
            </button>
            <button type="reset" class="btn btn-reset" title="Formular zurücksetzen">
                <i class="fas fa-undo"></i> Zurücksetzen
            </button>
        </div>
    </form>
    <?php endif; ?>

    <!-- Link zur Rezeptverwaltung -->
    <a href="view_recipes.php" class="btn btn-view" title="Zurück zur Rezeptverwaltung">
        <i class="fas fa-arrow-left"></i> Zurück
    </a>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
