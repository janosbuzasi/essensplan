<?php
$title = "Mahlzeitenkategorie löschen";
require_once 'auth.php'; // Überprüfung der Benutzeranmeldung (auth.php einbinden)
require '../header.php'; // Header einfügen
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

$error = ""; // Variable für Fehlermeldungen
$success = ""; // Variable für Erfolgsmeldungen

// Überprüfen, ob eine Kategorie übergeben wurde
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $categoryToDelete = $_GET['category'];

    // Überprüfung, ob die Kategorie in der Datenbank existiert
    $stmt = $conn->prepare("SELECT COUNT(*) FROM meal_categories WHERE name = ?");
    $stmt->execute([$categoryToDelete]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        // Kategorie existiert, Löschung vorbereiten
        if ($_POST && isset($_POST['confirm_delete']) && $_POST['confirm_delete'] === 'yes') {
            try {
                // Kategorie aus der Tabelle meal_categories löschen
                $stmt = $conn->prepare("DELETE FROM meal_categories WHERE name = ?");
                $stmt->execute([$categoryToDelete]);
                $success = "Die Kategorie wurde erfolgreich gelöscht.";
            } catch (PDOException $e) {
                $error = "Fehler beim Löschen der Kategorie: " . $e->getMessage();
            }
        }
    } else {
        $error = "Diese Kategorie existiert nicht.";
    }
} else {
    $error = "Keine Kategorie ausgewählt.";
}
?>

<main>
    <h2><?php echo $title; ?></h2>
    
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php elseif ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
        <a href="view_categories.php" class="btn btn-back">Zurück zur Kategorienübersicht</a>
    <?php else: ?>
        <p>Möchtest du die Kategorie <strong><?php echo htmlspecialchars($categoryToDelete, ENT_QUOTES); ?></strong> wirklich löschen?</p>
        <form method="post">
            <input type="hidden" name="confirm_delete" value="yes">
            <input type="submit" value="Ja, löschen" class="btn btn-delete">
            <a href="view_categories.php" class="btn btn-back">Abbrechen</a>
        </form>
    <?php endif; ?>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
