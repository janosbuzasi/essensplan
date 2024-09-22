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
        <label for="week_name">Name der Woche:</label>
        <input type="text" name="week_name" required><br>
        <label for="description">Beschreibung:</label>
        <textarea name="description"></textarea><br>
        <input type="submit" value="Wochenplan hinzufügen">
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO essensplan (week_number, year, week_name, description) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$_POST['week_number'], $_POST['year'], $_POST['week_name'], $_POST['description']])) {
            echo "<p>Wochenplan erfolgreich hinzugefügt!</p>";
        } else {
            echo "<p>Fehler beim Hinzufügen des Wochenplans.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
