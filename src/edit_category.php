<?php
$title = "Mahlzeitenkategorie bearbeiten";
require_once 'auth.php'; // Überprüfung der Benutzeranmeldung (auth.php einbinden)
require '../header.php'; // Header einfügen
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

$error = ""; // Variable für Fehlermeldungen
$success = ""; // Variable für Erfolgsmeldungen

// Überprüfen, ob eine Kategorie übergeben wurde
if (isset($_GET['category'])) {
    $currentCategory = $_GET['category'];

    // Formularverarbeitung
    if ($_POST) {
        $newCategory = trim($_POST['new_category']);

        // Validierung: Neue Kategorie darf nicht leer sein
        if (empty($newCategory)) {
            $error = "Die neue Kategorie darf nicht leer sein.";
        } else {
            // Überprüfen, ob die neue Kategorie bereits existiert
            $stmt = $conn->prepare("SELECT COUNT(*) FROM recipes WHERE category = ?");
            $stmt->execute([$newCategory]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $error = "Diese Kategorie existiert bereits.";
            } else {
                // Aktualisierung der Kategorie in der Datenbank
                try {
                    $stmt = $conn->prepare("UPDATE recipes SET category = ? WHERE category = ?");
                    $stmt->execute([$newCategory, $currentCategory]);
                    $success = "Die Kategorie wurde erfolgreich aktualisiert.";
                    $currentCategory = $newCategory; // Update the current category displayed
                } catch (PDOException $e) {
                    $error = "Fehler beim Aktualisieren der Kategorie: " . $e->getMessage();
                }
            }
        }
    }
} else {
    $error = "Keine Kategorie zum Bearbeiten ausgewählt.";
}
?>

<main>
    <h2><?php echo $title; ?></h2>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (isset($currentCategory)): ?>
        <form method="post" class="category-form"> <!-- CSS-Klasse für das Formular -->
            <div class="form-group">
                <label for="current_category">Aktuelle Kategorie:</label>
                <input type="text" name="current_category" value="<?php echo htmlspecialchars($currentCategory, ENT_QUOTES); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="new_category">Neue Kategorie:</label>
                <input type="text" name="new_category" placeholder="Neue Kategorie eingeben" required>
            </div>

            <input type="submit" value="Kategorie aktualisieren" class="btn btn-edit">
        </form>
    <?php endif; ?>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
