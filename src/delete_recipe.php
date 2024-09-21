<?php
$title = "Rezept löschen"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        $stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($recipe) {
            if (isset($_POST['confirm_delete'])) {
                $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                echo "<p>Rezept '" . $recipe['title'] . "' wurde gelöscht.</p>";
            } else {
                ?>
                <form action="delete_recipe.php?id=<?php echo $recipe['id']; ?>" method="post">
                    <p>Bist du sicher, dass du das Rezept '<?php echo $recipe['title']; ?>' löschen möchtest?</p>
                    <input type="submit" name="confirm_delete" value="Ja, löschen">
                    <a href="view_recipes.php">Abbrechen</a>
                </form>
                <?php
            }
        } else {
            echo "<p>Rezept nicht gefunden.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
