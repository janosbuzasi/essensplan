<?php
$title = "Rezepte verwalten";
require '../header.php'; // Header einfügen
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier kannst du die bestehenden Rezepte anzeigen, bearbeiten oder löschen.</p>
    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Vorhandene Rezepte abrufen
    $stmt = $conn->query("SELECT * FROM recipes ORDER BY title ASC");
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($recipes) {
        echo "<table class='styled-table'>"; // CSS-Klasse für Styling
        echo "<thead><tr><th>Titel</th><th>Kategorie</th><th>Zutaten</th><th>Zubereitung</th><th>Aktionen</th></tr></thead><tbody>";
        foreach ($recipes as $recipe) {
            echo "<tr>";
            echo "<td>" . $recipe['title'] . "</td>";
            echo "<td>" . $recipe['category'] . "</td>";
            echo "<td>" . nl2br($recipe['ingredients']) . "</td>"; // Zeilenumbrüche in den Zutaten anzeigen
            echo "<td>" . nl2br($recipe['instructions']) . "</td>"; // Zeilenumbrüche in der Zubereitung anzeigen
            echo "<td>";
            echo "<a href='edit_recipe.php?id=" . $recipe['id'] . "' class='btn btn-edit'>Bearbeiten</a> ";
            echo "<a href='delete_recipe.php?id=" . $recipe['id'] . "' class='btn btn-delete' onclick=\"return confirm('Möchtest du dieses Rezept wirklich löschen?');\">Löschen</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Keine Rezepte gefunden.</p>";
    }
    ?>

    <!-- Link zum Hinzufügen eines neuen Rezepts -->
    <a href="add_recipe.php" class="btn btn-add">Neues Rezept hinzufügen</a>
</main>
<?php
include '../footer.php'; // Footer einfügen
?>
