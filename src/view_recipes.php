<?php
$title = "Alle Rezepte anzeigen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <form method="GET" action="view_recipes.php">
        <label for="category">Kategorie filtern:</label>
        <input type="text" name="category" value="<?php echo isset($_GET['category']) ? $_GET['category'] : ''; ?>">
        <input type="submit" value="Filtern">
    </form>
    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM recipes";
    $params = [];

    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $query .= " WHERE category LIKE ?";
        $params[] = '%' . $_GET['category'] . '%';
    }

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($recipes) {
        echo "<ul>";
        foreach ($recipes as $recipe) {
            echo "<li>" . $recipe['title'] . " - <a href='edit_recipe.php?id=" . $recipe['id'] . "'>Bearbeiten</a> | <a href='delete_recipe.php?id=" . $recipe['id'] . "' onclick=\"return confirm('Möchtest du dieses Rezept wirklich löschen?');\">Löschen</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Keine Rezepte gefunden.</p>";
    }
    ?>
</main>
<?php
include '../footer.php';
?>
