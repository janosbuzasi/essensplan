<?php
$title = "Rezepte verwalten";
require_once 'auth.php'; // Überprüfung der Benutzeranmeldung (auth.php einbinden)
require '../header.php'; // Header einfügen
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier kannst du die bestehenden Rezepte anzeigen, bearbeiten oder löschen.</p>

    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Mahlzeitenkategorien abrufen
    $stmtCategories = $conn->query("SELECT DISTINCT category FROM recipes ORDER BY category ASC");
    $categories = $stmtCategories->fetchAll(PDO::FETCH_COLUMN);

    // Ausgewählte Kategorie abrufen
    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';

    // SQL-Abfrage basierend auf der ausgewählten Kategorie erstellen
    if ($selectedCategory) {
        $stmt = $conn->prepare("SELECT * FROM recipes WHERE category = ? ORDER BY title ASC");
        $stmt->execute([$selectedCategory]);
    } else {
        $stmt = $conn->query("SELECT * FROM recipes ORDER BY title ASC");
    }
    $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Filterformular für Kategorien
    ?>
    <form method="get" action="view_recipes.php" class="recipe-filter-form">
        <label for="category">Kategorie filtern:</label>
        <select name="category" id="category" onchange="this.form.submit()">
            <option value="">Alle Kategorien</option>
            <?php
            foreach ($categories as $category) {
                echo "<option value='$category'" . ($category == $selectedCategory ? " selected" : "") . ">$category</option>";
            }
            ?>
        </select>
        <noscript><input type="submit" value="Filtern"></noscript> <!-- Für Browser ohne JS -->
    </form>

    <?php
    // Gefilterte Kategorie anzeigen
    if ($selectedCategory) {
        echo "<p>Gefilterte Kategorie: <strong>" . htmlspecialchars($selectedCategory, ENT_QUOTES) . "</strong></p>";
    }

    if ($recipes) {
        echo "<table class='styled-table'>"; // CSS-Klasse für Styling
        echo "<thead><tr><th>Titel</th><th>Kategorie</th><th>Zutaten</th><th>Zubereitung</th><th>Aktionen</th></tr></thead><tbody>";
        foreach ($recipes as $recipe) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($recipe['title'], ENT_QUOTES) . "</td>";
            echo "<td>" . htmlspecialchars($recipe['category'], ENT_QUOTES) . "</td>";
            echo "<td>" . nl2br(htmlspecialchars($recipe['ingredients'], ENT_QUOTES)) . "</td>"; // Zeilenumbrüche in den Zutaten anzeigen
            echo "<td>" . nl2br(htmlspecialchars($recipe['instructions'], ENT_QUOTES)) . "</td>"; // Zeilenumbrüche in der Zubereitung anzeigen
            echo "<td>";
            echo "<a href='edit_recipe.php?id=" . $recipe['id'] . "' class='btn btn-edit' title='Bearbeiten'><i class='fas fa-edit'></i></a> ";
            echo "<a href='delete_recipe.php?id=" . $recipe['id'] . "' class='btn btn-delete' title='Löschen' onclick=\"return confirm('Möchtest du dieses Rezept wirklich löschen?');\"><i class='fas fa-trash-alt'></i></a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Keine Rezepte gefunden.</p>";
    }
    ?>

    <!-- Link zum Hinzufügen eines neuen Rezepts -->
    <a href="add_recipe.php" class="btn btn-add" title="Neues Rezept hinzufügen"><i class="fas fa-plus-circle"></i> Neues Rezept hinzufügen</a>
</main>
<?php
include '../footer.php'; // Footer einfügen
?>
