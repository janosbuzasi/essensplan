<?php
$title = "Rezepte anzeigen"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier werden alle vorhandenen Rezepte angezeigt:</p>
    <ul>
        <?php
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Abruf der vorhandenen Rezepte
        $stmt = $conn->query("SELECT * FROM recipes ORDER BY title");
        $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($recipes)) {
            foreach ($recipes as $recipe) {
                echo "<li>" . $recipe['title'] . " - <a href='edit_recipe.php?id=" . $recipe['id'] . "'>Bearbeiten</a> | <a href='delete_recipe.php?id=" . $recipe['id'] . "' onclick=\"return confirm('Möchtest du dieses Rezept wirklich löschen?');\">Löschen</a></li>";
            }
        } else {
            echo "<li>Keine Rezepte vorhanden.</li>";
        }
        ?>
    </ul>
    <a href="add_recipe.php">Neues Rezept hinzufügen</a>
</main>
<?php
include '../footer.php';
?>
