<?php
$title = "Neuen Wochenplan hinzufügen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <form action="add_week.php" method="post">
        <label for="week_number">Kalenderwoche:</label>
        <input type="number" name="week_number" min="1" max="52" required><br>
        <label for="year">Jahr:</label>
        <input type="number" name="year" value="<?php echo date('Y'); ?>" required><br>
        <input type="submit" value="Wochenplan hinzufügen">
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Überprüfen, ob der Wochenplan bereits existiert
        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE week_number = ? AND year = ?");
        $stmt->execute([$_POST['week_number'], $_POST['year']]);
        $existingWeekPlan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingWeekPlan) {
            echo "<p>Ein Wochenplan für Woche " . $_POST['week_number'] . " im Jahr " . $_POST['year'] . " existiert bereits.</p>";
        } else {
            // Wochenplan hinzufügen
            $stmt = $conn->prepare("INSERT INTO essensplan (week_number, year) VALUES (?, ?)");
            if ($stmt->execute([$_POST['week_number'], $_POST['year']])) {
                echo "<p>Wochenplan erfolgreich hinzugefügt!</p>";
            } else {
                echo "<p>Fehler beim Hinzufügen des Wochenplans.</p>";
            }
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
