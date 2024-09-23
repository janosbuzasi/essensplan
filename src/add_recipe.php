<?php
$title = "Neues Rezept hinzufügen";
require '../header.php'; // Header einfügen

require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

$error = ""; // Variable für Fehlermeldungen

if ($_POST) {
    // Rezeptdaten aus dem Formular abrufen
    $title = $_POST['title'];
    $category = $_POST['category'];
    $ingredients = $_POST['ingredients'];
    $instructions = $_POST['instructions'];
    $prep_time = $_POST['prep_time'];
    $cook_time = $_POST['cook_time'];
    $difficulty = $_POST['difficulty'];
    $servings = $_POST['servings'];

    // Validierung der Felder für Vorbereitungszeit, Kochzeit und Portionen
    if (!is_numeric($prep_time) || $prep_time <= 0) {
        $error .= "Bitte eine gültige Vorbereitungszeit (größer als 0) angeben.<br>";
    }

    if (!is_numeric($cook_time) || $cook_time <= 0) {
        $error .= "Bitte eine gültige Kochzeit (größer als 0) angeben.<br>";
    }

    if (!is_numeric($servings) || $servings <= 0) {
        $error .= "Bitte eine gültige Anzahl von Portionen (größer als 0) angeben.<br>";
    }

    // Wenn kein Fehler aufgetreten ist, Rezept in die Datenbank einfügen
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO recipes (title, category, ingredients, instructions, prep_time, cook_time, difficulty, servings) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $ingredients, $instructions, $prep_time, $cook_time, $difficulty, $servings]);
        echo "<p>Das Rezept wurde erfolgreich hinzugefügt.</p>";
    } else {
        // Fehlermeldungen anzeigen
        echo "<p style='color:red;'>$error</p>";
    }
}
?>

<main>
    <h2><?php echo $title; ?></h2>
    <form method="post">
        <label for="title">Titel des Rezepts:</label>
        <input type="text" name="title" required>

        <label for="category">Kategorie:</label>
        <input type="text" name="category">

        <label for="ingredients">Zutaten:</label>
        <textarea name="ingredients" required></textarea>

        <label for="instructions">Zubereitung:</label>
        <textarea name="instructions" required></textarea>

        <label for="prep_time">Vorbereitungszeit (Minuten):</label>
        <input type="number" name="prep_time" min="1" required>

        <label for="cook_time">Kochzeit (Minuten):</label>
        <input type="number" name="cook_time" min="1" required>

        <label for="difficulty">Schwierigkeitsgrad:</label>
        <input type="text" name="difficulty">

        <label for="servings">Portionen:</label>
        <input type="number" name="servings" min="1" required>

        <input type="submit" value="Rezept hinzufügen" class="btn btn-add">
    </form>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
