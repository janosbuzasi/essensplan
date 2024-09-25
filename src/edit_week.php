<?php
$title = "Wochenplan bearbeiten";
require_once 'auth.php'; // Überprüfung der Benutzeranmeldung (auth.php einbinden)
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
            <form action="edit_week.php?id=<?php echo $weekPlan['id']; ?>" method="post" class="recipe-form">
                <div class="form-group">
                    <label for="week_number">Kalenderwoche:</label>
                    <input type="number" name="week_number" value="<?php echo $weekPlan['week_number']; ?>" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="year">Jahr:</label>
                    <input type="number" name="year" value="<?php echo $weekPlan['year']; ?>" required class="form-control">
                </div>

                <div class="form-group">
                    <label for="description">Beschreibung:</label>
                    <textarea name="description" rows="4" class="form-control"><?php echo $weekPlan['description']; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" required class="form-select">
                        <option value="aktiv" <?php echo ($weekPlan['status'] == 'aktiv') ? 'selected' : ''; ?>>Aktiv</option>
                        <option value="archiviert" <?php echo ($weekPlan['status'] == 'archiviert') ? 'selected' : ''; ?>>Archiviert</option>
                    </select>
                </div>

                <input type="submit" value="Änderungen speichern" class="btn btn-edit">
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
    <a href="view_weeks.php" class="btn btn-view">Zurück zur Wochenplanverwaltung</a>
</main>
<?php
include '../footer.php';
?>
