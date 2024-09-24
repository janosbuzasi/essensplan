<?php
$title = "Rezeptzuordnung bearbeiten";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Überprüfung, ob eine Zuordnung ausgewählt wurde
    if (isset($_GET['id'])) {
        $assignmentId = $_GET['id'];

        // Die bestehende Zuordnung abrufen
        $stmt = $conn->prepare("
            SELECT er.*, wp.week_number, wp.year, mc.name AS meal_category_name, r.title AS recipe_title
            FROM essensplan_recipes er
            JOIN essensplan wp ON er.essensplan_id = wp.id
            JOIN meal_categories mc ON er.meal_category_id = mc.id
            JOIN recipes r ON er.recipe_id = r.id
            WHERE er.id = ?
        ");
        $stmt->execute([$assignmentId]);
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($assignment) {
            // Vorhandene Wochenpläne, Rezepte und Mahlzeitenkategorien abrufen
            $weekPlans = $conn->query("SELECT * FROM essensplan ORDER BY year, week_number")->fetchAll(PDO::FETCH_ASSOC);
            $recipes = $conn->query("SELECT * FROM recipes ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
            $mealCategories = $conn->query("SELECT * FROM meal_categories ORDER BY FIELD(name, 'Frühstück', 'Znüni', 'Mittagessen', 'Zvieri', 'Abendessen')")->fetchAll(PDO::FETCH_ASSOC);
            
            // Bearbeitungsformular anzeigen
            ?>
            <form action="edit_assignment.php?id=<?php echo $assignment['id']; ?>" method="post" class="recipe-form">
                <div class="form-group">
                    <label for="week_plan_id">Woche:</label>
                    <select name="week_plan_id" required class="form-select">
                        <?php foreach ($weekPlans as $plan): ?>
                            <option value="<?php echo $plan['id']; ?>" <?php echo $plan['id'] == $assignment['essensplan_id'] ? 'selected' : ''; ?>>
                                Woche <?php echo $plan['week_number'] . " im Jahr " . $plan['year']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="day_of_week">Tag:</label>
                    <select name="day_of_week" required class="form-select">
                        <option value="Montag" <?php echo $assignment['day_of_week'] == 'Montag' ? 'selected' : ''; ?>>Montag</option>
                        <option value="Dienstag" <?php echo $assignment['day_of_week'] == 'Dienstag' ? 'selected' : ''; ?>>Dienstag</option>
                        <option value="Mittwoch" <?php echo $assignment['day_of_week'] == 'Mittwoch' ? 'selected' : ''; ?>>Mittwoch</option>
                        <option value="Donnerstag" <?php echo $assignment['day_of_week'] == 'Donnerstag' ? 'selected' : ''; ?>>Donnerstag</option>
                        <option value="Freitag" <?php echo $assignment['day_of_week'] == 'Freitag' ? 'selected' : ''; ?>>Freitag</option>
                        <option value="Samstag" <?php echo $assignment['day_of_week'] == 'Samstag' ? 'selected' : ''; ?>>Samstag</option>
                        <option value="Sonntag" <?php echo $assignment['day_of_week'] == 'Sonntag' ? 'selected' : ''; ?>>Sonntag</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="meal_category_id">Mahlzeitenkategorie:</label>
                    <select name="meal_category_id" required class="form-select">
                        <?php foreach ($mealCategories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $assignment['meal_category_id'] ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="recipe_id">Rezept:</label>
                    <select name="recipe_id" required class="form-select">
                        <?php foreach ($recipes as $recipe): ?>
                            <option value="<?php echo $recipe['id']; ?>" <?php echo $recipe['id'] == $assignment['recipe_id'] ? 'selected' : ''; ?>>
                                <?php echo $recipe['title']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="submit" value="Änderungen speichern" class="btn btn-add">
            </form>
            <?php
        } else {
            echo "<p>Rezeptzuordnung nicht gefunden.</p>";
        }
    } else {
        echo "<p>Keine Rezeptzuordnung ausgewählt.</p>";
    }

    // Verarbeitung der Formularübermittlung
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $weekPlanId = $_POST['week_plan_id'];
        $dayOfWeek = $_POST['day_of_week'];
        $mealCategoryId = $_POST['meal_category_id'];
        $recipeId = $_POST['recipe_id'];

        // Update der Rezeptzuordnung
        $stmt = $conn->prepare("UPDATE essensplan_recipes SET essensplan_id = ?, day_of_week = ?, meal_category_id = ?, recipe_id = ? WHERE id = ?");
        if ($stmt->execute([$weekPlanId, $dayOfWeek, $mealCategoryId, $recipeId, $assignmentId])) {
            echo "<p>Rezeptzuordnung erfolgreich aktualisiert!</p>";
        } else {
            echo "<p>Fehler beim Aktualisieren der Rezeptzuordnung.</p>";
        }
    }
    ?>
    <a href="assign_recipe_to_week.php" class="btn btn-view">Zurück zur Zuordnungsseite</a>
</main>
<?php
include '../footer.php';
?>
