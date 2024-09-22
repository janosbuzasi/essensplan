<?php
$title = "Rezept löschen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Überprüfung, ob das Rezept existiert
        $stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($recipe) {
            // Bestätigungsabfrage anzeigen
            echo "<p>Möchtest du das Rezept <strong>" . $recipe['title'] . "</strong> wirklich löschen?</p>";
            echo '<form action="delete_recipe.php?id=' . $recipe['id'] . '" method="post">
                    <button type="submit" name="confirm_delete" value="yes">Ja, löschen</button>
                    <button type="submit" name="confirm_delete" value="no">Nein, abbrechen</button>
                  </form>';

            // Verarbeitung der Benutzerentscheidung
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if ($_POST['confirm_delete'] === 'yes') {
                    $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
                    if ($stmt->execute([$_GET['id']])) {
                        echo "<p>Rezept erfolgreich gelöscht!</p>";
                    } else {
                        echo "<p>Fehler beim Löschen des Rezepts.</p>";
                    }
                } else {
                    echo "<p>Löschvorgang abgebrochen.</p>";
                }
            }
        } else {
            echo "<p>Rezept nicht gefunden.</p>";
        }
    } else {
        echo "<p>Kein Rezept ausgewählt.</p>";
    }
    ?>
</main>
<?php
include '../footer.php';
?>
