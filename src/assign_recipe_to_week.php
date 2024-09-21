<?php
$title = "Rezept zu Essensplan zuordnen"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Aktuellen Essensplan abrufen
        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plan) {
            echo "<h3>Essensplan: Woche " . $plan['week_number'] . " im Jahr " . $plan['year'] . "</h3>";

            // Rezepte abrufen
            $stmt = $conn->query("SELECT * FROM recipes ORDER BY title");
            $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($recipes)) {
                ?>
                <form action="assign_recipe_to_week.php?id=<?php echo $plan['id']; ?>" method="post">
                    <label for="recipe_id">Rezept:</label>
                    <select name="recipe_id">
                        <?php
                        foreach ($recipes as $recipe) {
                            echo "<option value='" . $recipe['id'] . "'>" . $recipe['title'] . "</option>";
                        }
                        ?>
                    </select><br>
                    <label for="day_of_week">Tag:</label>
                    <select name="day_of_week">
                        <option value="Montag">Montag</option>
                        <option value="Dienstag">Dienstag</option>
                        <option value="Mittwoch">Mittwoch</option>
                        <option value="Donnerstag">Donnerstag</option>
                        <option value="Freitag">Freitag</option>
                        <option value="Samstag">Samstag</option>
                        <option value="Sonntag">Sonntag</option>
                    </select><br>
                    <label for="meal_type">Mahlzeit:</label>
                    <select name="meal_type">
                        <option value="Frühstück">Frühstück</option>
                        <option value="Mittagessen">Mittagessen</option>
                        <option value="Abendessen">Abendessen</option>
                    </select><br>
                    <input type="submit" value="Rezept zuordnen">
                </form>
                <?php
            } else {
                echo "<p>Keine Rezepte verfügbar.</p>";
            }
        } else {
            echo "<p>Essensplan nicht gefunden.</p>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $stmt = $conn->prepare("INSERT INTO essensplan_recipes (essensplan_id, recipe_id, day_of_week, meal_type) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$_GET['id'], $_POST['recipe_id'], $_POST['day_of_week'], $_POST['meal_type']])) {
            echo "<p>Rezept erfolgreich zugeordnet!</p>";
        } else {
            echo "<p>Fehler beim Zuordnen des Rezepts.</p>";
        }
    }
    ?>
</main>
<?php
include '../footer.php';
?>
