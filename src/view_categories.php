<?php
$title = "Mahlzeitenkategorien anzeigen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->query("SELECT * FROM meal_categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($categories) {
        echo "<ul>";
        foreach ($categories as $category) {
            echo "<li>" . $category['name'] . " - <a href='edit_category.php?id=" . $category['id'] . "'>Bearbeiten</a> | <a href='delete_category.php?id=" . $category['id'] . "' onclick=\"return confirm('Möchtest du diese Kategorie wirklich löschen?');\">Löschen</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Keine Kategorien gefunden.</p>";
    }
    ?>
    <a href="add_category.php">Neue Kategorie hinzufügen</a>
</main>
<?php
include '../footer.php';
?>
