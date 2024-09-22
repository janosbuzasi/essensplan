<?php
$title = "Mahlzeitenkategorie löschen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Überprüfung, ob die Kategorie existiert
        $stmt = $conn->prepare("SELECT * FROM meal_categories WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            // Bestätigungsabfrage anzeigen
            echo "<p>Möchtest du die Mahlzeitenkategorie <strong>" . $category['name'] . "</strong> wirklich löschen?</p>";
            echo '<form action="delete_category.php?id=' . $category['id'] . '" method="post">
                    <button type="submit" name="confirm_delete" value="yes">Ja, löschen</button>
                    <button type="submit" name="confirm_delete" value="no">Nein, abbrechen</button>
                  </form>';

            // Verarbeitung der Benutzerentscheidung
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if ($_POST['confirm_delete'] === 'yes') {
                    $stmt = $conn->prepare("DELETE FROM meal_categories WHERE id = ?");
                    if ($stmt->execute([$_GET['id']])) {
                        echo "<p>Kategorie erfolgreich gelöscht!</p>";
                    } else {
                        echo "<p>Fehler beim Löschen der Kategorie.</p>";
                    }
                } else {
                    echo "<p>Löschvorgang abgebrochen.</p>";
                }
            }
        } else {
            echo "<p>Kategorie nicht gefunden.</p>";
        }
    } else {
        echo "<p>Keine Kategorie ausgewählt.</p>";
    }
    ?>
</main>
<?php
include '../footer.php';
?>
