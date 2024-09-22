<?php
$title = "Rezept zu Woche zuordnen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Vorhandene Wochenpläne abrufen
    $weekPlans = $conn->query("SELECT * FROM essensplan ORDER BY year DESC, week_number DESC")->fetchAll(PDO::FETCH_ASSOC);
    $recipes = $conn->query("SELECT * FROM recipes ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);
    $mealCategories = $conn->query("SELECT * FROM meal_categories ORDER BY FIELD(name, 'Frühstück', 'Znüni', 'Mittagessen', 'Zvieri', 'Abendessen')")->fetchAll(PDO::FETCH_ASSOC);

    // Bestimme die standardmäßig ausgewählte Woche (die neueste)
    $selectedWeekPlanId = isset($_GET['week_plan_id']) ? $_GET['week_plan_id'] : (isset($weekPlans[0]['id']) ? $weekPlans[0]['id'] : null);

    if ($weekPlans && $recipes && $mealCategories) {
        ?>
        <!-- Dropdown-Menü zur Auswahl der Woche -->
        <form method="get" action="assign_recipe_to_week.php">
            <label for="week_plan_id">Woche:</label>
            <select name="week_plan_id" onchange="this.form.submit()" required>
                <?php foreach ($weekPlans as $plan): ?>
                    <option value="<?php echo $plan['id']; ?>" <?php echo ($plan['id'] == $selectedWeekPlanId) ? 'selected' : ''; ?>>
                        Woche <?php echo $plan['week_number'] . " im Jahr " . $plan['year']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <br>

        <!-- Formular zur Zuordnung eines Rezepts -->
        <form action="assign_recipe_to_week.php" method="post">
            <input type="hidden" name="week_plan_id" value="<?php echo $selectedWeekPlanId; ?>">
            
            <label for="day_of_week">Tag:</label>
            <select name="day_of_week" required>
                <option value="Montag">Montag</option>
                <option value="Dienstag">Dienstag</option>
                <option value="Mittwoch">Mittwoch</option>
                <option value="Donnerstag">Donnerstag</option>
                <option value="Freitag">Freitag</option>
                <option value="Samstag">Samstag</option>
                <option value="Sonntag">Sonntag</option>
            </select><br>

            <label for="meal_category_id">Mahlzeitenkategorie:</label>
            <select name="meal_category_id" required>
                <?php foreach ($mealCategories as $category): ?>
                    <option value="<?php echo $category['id']; ?>">
                        <?php echo $category['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="recipe_id">Rezept:</label>
            <select name="recipe_id" required>
                <?php foreach ($recipes as $recipe): ?>
                    <option value="<?php echo $recipe['id']; ?>">
                        <?php echo $recipe['title']; ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <input type="submit" value="Rezept zuordnen">
        </form>
        <?php
    } else {
        echo "<p>Keine Wochenpläne, Rezepte oder Mahlzeitenkategorien verfügbar.</p>";
    }

    // Verarbeitung des Formulars
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $weekPlanId = $_POST['week_plan_id'];
        $dayOfWeek = $_POST['day_of_week'];
        $mealCategoryId = $_POST['meal_category_id'];
        $recipeId = $_POST['recipe_id'];

        // Überprüfung, ob die Zuordnung bereits existiert
        $stmt = $conn->prepare("SELECT * FROM essensplan_recipes WHERE essensplan_id = ? AND day_of_week = ? AND meal_category_id = ?");
        $stmt->execute([$weekPlanId, $dayOfWeek, $mealCategoryId]);
        $existingAssignment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingAssignment) {
            echo "<p>Für diese Kombination aus Woche, Tag und Mahlzeitenkategorie existiert bereits ein Rezept.</p>";
        } else {
            // Zuordnung in die Datenbank einfügen
            $stmt = $conn->prepare("INSERT INTO essensplan_recipes (essensplan_id, recipe_id, day_of_week, meal_category_id) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$weekPlanId, $recipeId, $dayOfWeek, $mealCategoryId])) {
                echo "<p>Rezept erfolgreich zugeordnet!</p>";
            } else {
                echo "<p>Fehler beim Zuordnen des Rezepts.</p>";
            }
        }
    }

    // Bestehende Zuordnungen anzeigen
    echo "<h3>Bestehende Zuordnungen</h3>";
    if ($selectedWeekPlanId) {
        $stmt = $conn->prepare("
            SELECT er.id, wp.week_number, wp.year, er.day_of_week, mc.name AS meal_category, r.title AS recipe_title
            FROM essensplan_recipes er
            JOIN essensplan wp ON er.essensplan_id = wp.id
            JOIN recipes r ON er.recipe_id = r.id
            JOIN meal_categories mc ON er.meal_category_id = mc.id
            WHERE er.essensplan_id = ?
            ORDER BY FIELD(er.day_of_week, 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
                     FIELD(mc.name, 'Frühstück', 'Znüni', 'Mittagessen', 'Zvieri', 'Abendessen')
        ");
        $stmt->execute([$selectedWeekPlanId]);
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($assignments) {
            echo "<table border='1'>";
            echo "<tr><th>Tag</th><th>Kategorie</th><th>Rezept</th><th>Aktion</th></tr>";
            foreach ($assignments as $assignment) {
                echo "<tr>
                        <td>" . $assignment['day_of_week'] . "</td>
                        <td>" . $assignment['meal_category'] . "</td>
                        <td>" . $assignment['recipe_title'] . "</td>
                        <td><a href='edit_assignment.php?id=" . $assignment['id'] . "'>Bearbeiten</a></td>
                      </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>Keine Zuordnungen für diese Woche gefunden.</p>";
        }
    } else {
        echo "<p>Keine Woche ausgewählt.</p>";
    }
    ?>
</main>
<?php
include '../footer.php';
?>
