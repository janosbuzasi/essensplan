<?php
$title = "Neuen Essensplan hinzufügen"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <form action="add_week.php" method="post">
        <label for="week_number">Kalenderwoche:</label>
        <input type="number" name="week_number" required><br>
        <label for="year">Jahr:</label>
        <input type="number" name="year" required><br>
        <label for="week_name">Name des Essensplans:</label>
        <input type="text" name="week_name"><br>
        <label for="description">Beschreibung:</label>
        <textarea name="description"></textarea><br>
        <input type="submit" value="Essensplan hinzufügen">
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO essensplan (week_number, year, week_name, description, status) VALUES (?, ?, ?, ?, 'aktiv')");
        if ($stmt->execute([$_POST['week_number'], $_POST['year'], $_POST['week_name'], $_POST['description']])) {
            echo "<p>Essensplan erfolgreich hinzugefügt!</p>";
        } else {
            echo "<p>Fehler beim Hinzufügen des Essensplans.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
