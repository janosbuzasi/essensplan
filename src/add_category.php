<?php
$title = "Neue Mahlzeitenkategorie hinzufügen";
require '../header.php'; // Header einfügen
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

$error = ""; // Variable für Fehlermeldungen

if ($_POST) {
    // Kategorie aus dem Formular abrufen
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    // Validierung: Name darf nicht leer sein
    if (empty($name)) {
        $error = "Der Name der Kategorie darf nicht leer sein.";
    } else {
        // Überprüfen, ob die Kategorie bereits existiert
        $stmt = $conn->prepare("SELECT COUNT(*) FROM meal_categories WHERE name = ?");
        $stmt->execute([$name]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "Diese Kategorie existiert bereits.";
        } else {
            // Neue Kategorie in die Datenbank einfügen
            try {
                $stmt = $conn->prepare("INSERT INTO meal_categories (name, description) VALUES (?, ?)");
                $stmt->execute([$name, $description]);
                echo "<p>Die Kategorie wurde erfolgreich hinzugefügt.</p>";
            } catch (PDOException $e) {
                $error = "Fehler beim Hinzufügen der Kategorie: " . $e->getMessage();
            }
        }
    }
}
?>

<main>
    <h2><?php echo $title; ?></h2>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="post" class="category-form"> <!-- CSS-Klasse für das Formular -->
        <div class="form-group">
            <label for="name">Name der Kategorie:</label>
            <input type="text" name="name" placeholder="Name der Kategorie eingeben" required>
        </div>

        <div class="form-group">
            <label for="description">Beschreibung (optional):</label>
            <textarea name="description" placeholder="Beschreibung der Kategorie eingeben"></textarea>
        </div>

        <input type="submit" value="Kategorie hinzufügen" class="btn btn-add">
    </form>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
