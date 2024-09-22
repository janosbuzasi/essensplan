<?php
$title = "Wochenplan bearbeiten";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $weekPlan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($weekPlan) {
            ?>
            <form action="edit_week.php?id=<?php echo $weekPlan['id']; ?>" method="post">
                <label for="week_number">Kalenderwoche:</label>
                <input type="number" name="week_number" min="1" max="52" value="<?php echo $weekPlan['week_number']; ?>" required><br>
                <label for="year">Jahr:</label>
                <input type="number" name="year" value="<?php echo $weekPlan['year']; ?>" required><br>
                <label for="week_name">Name der Woche:</label>
                <input type="text" name="week_name" value="<?php echo $weekPlan['week_name']; ?>" required><br>
                <label for="description">Beschreibung:</label>
                <textarea name="description"><?php echo $weekPlan['description']; ?></textarea><br>
                <input type="submit" value="Wochenplan speichern">
            </form>
            <?php
        } else {
            echo "<p>Wochenplan nicht gefunden.</p>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("UPDATE essensplan SET week_number = ?, year = ?, week_name = ?, description = ? WHERE id = ?");
        if ($stmt->execute([$_POST['week_number'], $_POST['year'], $_POST['week_name'], $_POST['description'], $_GET['id']])) {
            echo "<p>Wochenplan erfolgreich aktualisiert!</p>";
        } else {
            echo "<p>Fehler beim Aktualisieren des Wochenplans.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
