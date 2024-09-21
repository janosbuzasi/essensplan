<?php
$title = "Kategorie löschen"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($category) {
            if (isset($_POST['confirm_delete'])) {
                $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                echo "<p>Kategorie wurde gelöscht.</p>";
            } else {
                ?>
                <form action="delete_category.php?id=<?php echo $category['id']; ?>" method="post">
                    <p>Bist du sicher, dass du die Kategorie '<?php echo $category['name']; ?>' löschen möchtest?</p>
                    <input type="submit" name="confirm_delete" value="Ja, löschen">
                    <a href="view_categories.php">Abbrechen</a>
                </form>
                <?php
            }
        } else {
            echo "<p>Kategorie nicht gefunden.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
