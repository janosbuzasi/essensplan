<?php
$title = "Rezept zu Woche zuordnen";
require_once 'auth.php'; // Überprüfung der Benutzeranmeldung (auth.php einbinden)
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

    // Bestimme die standardmäßig ausgewählte Woche (die neueste oder aus der URL)
    $selectedWeekPlanId = isset($_GET['week_plan_id']) ? $_GET['week_plan_id'] : (isset($weekPlans[0]['id']) ? $weekPlans[0]['id'] : null);

    // Alle verfügbaren Tage und Mahlzeitenkategorien
    $allDays = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];

    // Bereits zugewiesene Kombinationen abrufen
    $stmt = $conn->prepare("SELECT day_of_week, meal_category_id FROM essensplan_recipes WHERE essensplan_id = ?");
    $stmt->execute([$selectedWeekPlanId]);
    $existingAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verfügbare Kombinationen von Tagen und Mahlzeitenkategorien berechnen
    $availableDays = $allDays;
    $availableMealCategories = [];

    foreach ($allDays as $day) {
        $usedMealCategories = [];
        foreach ($existingAssignments as $assignment) {
            if ($assignment['day_of_week'] === $day) {
                $usedMealCategories[] = $assignment['meal_category_id'];
            }
        }

        // Finde die Mahlzeitenkategorien, die noch nicht verwendet wurden
        $remainingMealCategories = array_filter($mealCategories, function($category) use ($usedMealCategories) {
            return !in_array($category['id'], $usedMealCategories);
        });

        // Wenn es noch verfügbare Kategorien gibt, dann fügen wir den Tag und die Kategorien zur Verfügungsliste hinzu
        if (!empty($remainingMealCategories)) {
            $availableMealCategories[$day] = $remainingMealCategories;
        } else {
            // Entferne den Tag, wenn alle Kategorien bereits zugeordnet sind
            if (($key = array_search($day, $availableDays)) !== false) {
                unset($availableDays[$key]);
            }
        }
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
                echo "<script>
                        // Seite nach 1 Sekunde aktualisieren, um die Tabelle mit den Zuordnungen zu aktualisieren
                        setTimeout(function() {
                            window.location.href = 'assign_recipe_to_week.php?week_plan_id=' + $weekPlanId;
                        }, 1000);
                      </script>";
                exit();
            } else {
                echo "<p>Fehler beim Zuordnen des Rezepts.</p>";
            }
        }
    }
    ?>

    <!-- Dropdown-Menü zur Auswahl der Woche -->
    <form method="get" action="assign_recipe_to_week.php" class="form-inline">
        <label for="week_plan_id">Woche:</label>
        <select name="week_plan_id" onchange="this.form.submit()" required class="form-select">
            <?php foreach ($weekPlans as $plan): ?>
                <option value="<?php echo $plan['id']; ?>" <?php echo ($plan['id'] == $selectedWeekPlanId) ? 'selected' : ''; ?>>
                    Woche <?php echo $plan['week_number'] . " im Jahr " . $plan['year']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <br>

    <!-- Formular zur Zuordnung eines Rezepts -->
    <?php if (!empty($availableDays) && !empty($availableMealCategories)) : ?>
        <form action="assign_recipe_to_week.php" method="post" class="recipe-form">
            <input type="hidden" name="week_plan_id" value="<?php echo $selectedWeekPlanId; ?>">

            <div class="form-group">
                <label for="day_of_week">Tag:</label>
                <select name="day_of_week" required class="form-select">
                    <?php foreach ($availableDays as $day): ?>
                        <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="meal_category_id">Mahlzeitenkategorie:</label>
                <select name="meal_category_id" required class="form-select">
                    <?php foreach ($availableMealCategories as $day => $categories): ?>
                        <?php if ($day === reset($availableDays)): // Nur für den ersten verfügbaren Tag anzeigen ?>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>">
                                    <?php echo $category['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="recipe_id">Rezept:</label>
                <select name="recipe_id" required class="form-select">
                    <?php foreach ($recipes as $recipe): ?>
                        <option value="<?php echo $recipe['id']; ?>">
                            <?php echo $recipe['title']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="submit" value="Rezept zuordnen" class="btn btn-add">
        </form>
    <?php else: ?>
        <p>Alle Tage und Mahlzeitenkategorien sind für diese Woche bereits zugewiesen.</p>
    <?php endif; ?>

    <?php
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
            echo "<table class='styled-table'>";
            echo "<thead><tr><th>Tag</th><th>Kategorie</th><th>Rezept</th><th>Aktion</th></tr></thead><tbody>";
            foreach ($assignments as $assignment) {
                echo "<tr>
                        <td>" . $assignment['day_of_week'] . "</td>
                        <td>" . $assignment['meal_category'] . "</td>
                        <td>" . $assignment['recipe_title'] . "</td>
                        <td><a href='edit_assignment.php?id=" . $assignment['id'] . "' class='btn btn-edit'><i class='fas fa-edit'></i></a></td>
                      </tr>";
            }
            echo "</tbody></table>";
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
