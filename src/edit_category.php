<?php
$title = "Mahlzeitenkategorie bearbeiten";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM meal_categories WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            ?>
            <form action="edit_category.php?id=<?php echo $category['id']; ?>" method="post">
                <label for="name">Name der Kategorie:</label>
                <input type="text" name="name" value="<?php echo $category['name']; ?>" required><br>
                <label for="description">Beschreibung:</label>
                <textarea name="description"><?php echo $category['description']; ?></textarea><br>
                <input type="submit" value="Kategorie speichern">
            </form>
            <?php
        } else {
            echo "<p>Kategorie nicht gefunden.</p>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("UPDATE meal_categories SET name = ?, description = ? WHERE id = ?");
        if ($stmt->execute([$_POST['name'], $_POST['description'], $_GET['id']])) {
            echo "<p>Kategorie erfolgreich aktualisiert!</p>";
        } else {
            echo "<p>Fehler beim Aktualisieren der Kategorie.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
