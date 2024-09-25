<?php
$title = "Wochenplan anzeigen";
require_once 'auth.php'; // Überprüfung der Benutzeranmeldung (auth.php einbinden)
require '../header.php';
?>

<main>
    <h2><?php echo $title; ?></h2>

    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Wochenplan-ID abrufen
    $weekPlanId = isset($_GET['id']) ? intval($_GET['id']) : null;

    // Button zum Drucken des Wochenplans
    if ($weekPlanId): ?>
        <a href="print.php?id=<?php echo $weekPlanId; ?>" class="btn btn-print" target="_blank">
            <i class="fas fa-print"></i> Drucken
        </a>
    <?php endif; ?>

    <?php
    if ($weekPlanId) {
        // Wochenplan-Daten abrufen
        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
        $stmt->execute([$weekPlanId]);
        $weekPlan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($weekPlan) {
            echo "<div class='form-group'>";
            echo "<label>Wochenplan:</label> Woche " . $weekPlan['week_number'] . " im Jahr " . $weekPlan['year'];
            echo "</div>";

            echo "<div class='form-group'>";
            echo "<label>Beschreibung:</label> " . htmlspecialchars($weekPlan['description'] ?? '', ENT_QUOTES);
            echo "</div>";

            // Mahlzeitenplan anzeigen
            echo "<h3>Mahlzeitenplan</h3>";
            $stmt = $conn->prepare("
                SELECT er.id, er.day_of_week, mc.name AS meal_category, r.title AS recipe_title
                FROM essensplan_recipes er
                JOIN recipes r ON er.recipe_id = r.id
                JOIN meal_categories mc ON er.meal_category_id = mc.id
                WHERE er.essensplan_id = ?
                ORDER BY FIELD(er.day_of_week, 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
                         FIELD(mc.name, 'Frühstück', 'Znüni', 'Mittagessen', 'Zvieri', 'Abendessen')
            ");
            $stmt->execute([$weekPlanId]);
            $mealPlan = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($mealPlan) {
                echo "<table class='styled-table'>"; // CSS-Klasse für Styling
                echo "<thead><tr><th>Tag</th><th>Kategorie</th><th>Rezept</th></tr></thead><tbody>";
                foreach ($mealPlan as $meal) {
                    echo "<tr>
                            <td>" . $meal['day_of_week'] . "</td>
                            <td>" . $meal['meal_category'] . "</td>
                            <td>" . $meal['recipe_title'] . "</td>
                          </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>Keine Mahlzeitenzuordnungen für diesen Wochenplan gefunden.</p>";
            }

        } else {
            echo "<p>Wochenplan nicht gefunden.</p>";
        }
    } else {
        echo "<p>Kein Wochenplan ausgewählt.</p>";
    }
    ?>

    <!-- Link zum Bearbeiten des Wochenplans -->
    <a href="edit_week.php?id=<?php echo $weekPlanId; ?>" class="btn btn-edit" title="Wochenplan bearbeiten">
        <i class="fas fa-edit"></i>
    </a>
    <a href="assign_recipe_to_week.php?week_plan_id=<?php echo $weekPlanId; ?>" class="btn btn-add" title="Rezepte zuweisen">
        <i class="fas fa-plus-circle"></i>
    </a>
    <a href="view_weeks.php" class="btn btn-view" title="Zurück zur Übersicht">
        <i class="fas fa-arrow-circle-left"></i>
    </a>
</main>

<?php
include '../footer.php';
?>
