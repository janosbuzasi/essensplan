<?php
$title = "Rezept löschen";
require '../header.php'; // Header einfügen
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];
    
    // Rezeptdetails abrufen, um sie dem Benutzer anzuzeigen
    $stmt = $conn->prepare("SELECT title FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recipe) {
        // Bestätigungsabfrage anzeigen
        if (!isset($_POST['confirm'])) {
            ?>
            <main>
                <h2><?php echo $title; ?></h2>
                <p>Möchtest du das Rezept <strong><?php echo $recipe['title']; ?></strong> wirklich löschen?</p>
                <form method="post">
                    <input type="hidden" name="confirm" value="yes">
                    <button type="submit" class="btn btn-delete">Löschen</button>
                    <a href="view_recipes.php" class="btn btn-add">Abbrechen</a>
                </form>
            </main>
            <?php
        } else {
            // Rezept löschen
            $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
            $stmt->execute([$recipe_id]);
            echo "<main><p>Das Rezept <strong>{$recipe['title']}</strong> wurde erfolgreich gelöscht.</p>";
            echo "<a href='view_recipes.php' class='btn btn-add'>Zurück zur Rezeptverwaltung</a></main>";
        }
    } else {
        echo "<main><p>Rezept nicht gefunden.</p><a href='view_recipes.php' class='btn btn-add'>Zurück zur Rezeptverwaltung</a></main>";
    }
} else {
    echo "<main><p>Keine ID angegeben.</p><a href='view_recipes.php' class='btn btn-add'>Zurück zur Rezeptverwaltung</a></main>";
}

include '../footer.php'; // Footer einfügen
?>
