<?php
$title = "Wochenplan bearbeiten";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Überprüfung, ob eine ID übergeben wurde
    if (isset($_GET['id'])) {
        $weekPlanId = $_GET['id'];

        // Wochenplan abrufen
        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
        $stmt->execute([$weekPlanId]);
        $weekPlan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($weekPlan) {
            // Bearbeitungsformular anzeigen
            ?>
            <form action="edit_week.php?id=<?php echo $weekPlan['id']; ?>" method="post">
                <label for="week_number">Kalenderwoche:</label><br>
                <input type="number" name="week_number" value="<?php echo $weekPlan['week_number']; ?>" required><br><br>

                <label for="year">Jahr:</label><br>
                <input type="number" name="year" value="<?php echo $weekPlan['year']; ?>" required><br><br>

                <label for="description">Beschreibung:</label><br>
                <textarea name="description" rows="4"><?php echo $weekPlan['description']; ?></textarea><br><br>

                <label for="status">Status:</label><br>
                <select name="status" required>
                    <option value="aktiv" <?php echo ($weekPlan['status'] == 'aktiv') ? 'selected' : ''; ?>>Aktiv</option>
                    <option value="archiviert" <?php echo ($weekPlan['status'] == 'archiviert') ? 'selected' : ''; ?>>Archiviert</option>
                </select><br><br>

                <input type="submit" value="Änderungen speichern">
            </form>
            <?php
        } else {
            echo "<p>Wochenplan nicht gefunden.</p>";
        }
    } else {
        echo "<p>Keine Wochenplan-ID übergeben.</p>";
    }

    // Verarbeitung des Formulars
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $weekNumber = $_POST['week_number'];
        $year = $_POST['year'];
        $description = $_POST['description'];
        $status = $_POST['status'];

        // Wochenplan aktualisieren
        $stmt = $conn->prepare("UPDATE essensplan SET week_number = ?, year = ?, description = ?, status = ? WHERE id = ?");
        if ($stmt->execute([$weekNumber, $year, $description, $status, $weekPlanId])) {
            echo "<p>Wochenplan erfolgreich aktualisiert!</p>";
        } else {
            echo "<p>Fehler beim Aktualisieren des Wochenplans.</p>";
        }
    }
    ?>
    <a href="view_weeks.php">Zurück zur Wochenplanverwaltung</a>
</main>
<?php
include '../footer.php';
?>
