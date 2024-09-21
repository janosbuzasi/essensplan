<?php
$title = "Essensplan bearbeiten"; 
require '../header.php';  // Inkludiere den Header
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
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plan) {
            ?>
            <form action="edit_week.php?id=<?php echo $plan['id']; ?>" method="post">
                <label for="week_number">Kalenderwoche:</label>
                <input type="number" name="week_number" value="<?php echo $plan['week_number']; ?>" required><br>
                <label for="year">Jahr:</label>
                <input type="number" name="year" value="<?php echo $plan['year']; ?>" required><br>
                <label for="week_name">Name des Essensplans:</label>
                <input type="text" name="week_name" value="<?php echo $plan['week_name']; ?>"><br>
                <label for="description">Beschreibung:</label>
                <textarea name="description"><?php echo $plan['description']; ?></textarea><br>
                <input type="submit" value="Essensplan speichern">
            </form>
            <?php
        } else {
            echo "<p>Essensplan nicht gefunden.</p>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("UPDATE essensplan SET week_number = ?, year = ?, week_name = ?, description = ? WHERE id = ?");
        if ($stmt->execute([$_POST['week_number'], $_POST['year'], $_POST['week_name'], $_POST['description'], $_GET['id']])) {
            echo "<p>Essensplan erfolgreich aktualisiert!</p>";
        } else {
            echo "<p>Fehler beim Aktualisieren des Essensplans.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
