<?php
$title = "Wochenplan löschen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Überprüfung, ob der Wochenplan existiert
        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $weekPlan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($weekPlan) {
            // Bestätigungsabfrage anzeigen
            echo "<p>Möchtest du den Wochenplan <strong>Woche " . $weekPlan['week_number'] . " im Jahr " . $weekPlan['year'] . "</strong> wirklich löschen?</p>";
            echo '<form action="delete_week.php?id=' . $weekPlan['id'] . '" method="post">
                    <button type="submit" name="confirm_delete" value="yes">Ja, löschen</button>
                    <button type="submit" name="confirm_delete" value="no">Nein, abbrechen</button>
                  </form>';

            // Verarbeitung der Benutzerentscheidung
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if ($_POST['confirm_delete'] === 'yes') {
                    $stmt = $conn->prepare("DELETE FROM essensplan WHERE id = ?");
                    if ($stmt->execute([$_GET['id']])) {
                        echo "<p>Wochenplan erfolgreich gelöscht!</p>";
                    } else {
                        echo "<p>Fehler beim Löschen des Wochenplans.</p>";
                    }
                } else {
                    echo "<p>Löschvorgang abgebrochen.</p>";
                }
            }
        } else {
            echo "<p>Wochenplan nicht gefunden.</p>";
        }
    } else {
        echo "<p>Kein Wochenplan ausgewählt.</p>";
    }
    ?>
</main>
<?php
include '../footer.php';
?>
