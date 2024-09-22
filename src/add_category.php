<?php
$title = "Neue Mahlzeitenkategorie hinzufügen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <form action="add_category.php" method="post">
        <label for="name">Name der Kategorie:</label>
        <input type="text" name="name" required><br>
        <label for="description">Beschreibung:</label>
        <textarea name="description"></textarea><br>
        <input type="submit" value="Kategorie hinzufügen">
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("INSERT INTO meal_categories (name, description) VALUES (?, ?)");
        if ($stmt->execute([$_POST['name'], $_POST['description']])) {
            echo "<p>Kategorie erfolgreich hinzugefügt!</p>";
        } else {
            echo "<p>Fehler beim Hinzufügen der Kategorie.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
