<?php
$title = "Essensplan Druckansicht";
require '../config/db.php'; // Nur die Datenbankverbindung laden

$db = new Database();
$conn = $db->getConnection();

// Wochenplan-ID abrufen
$weekPlanId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($weekPlanId) {
    // Wochenplan-Daten abrufen
    $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
    $stmt->execute([$weekPlanId]);
    $weekPlan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($weekPlan) {
        // Berechnung des ersten Tages der Woche basierend auf der Kalenderwoche und dem Jahr
        $dateTime = new DateTime();
        $dateTime->setISODate($weekPlan['year'], $weekPlan['week_number']);
        $firstDayOfWeek = $dateTime->format('d.m.Y');
    }
} else {
    die("Kein Wochenplan ausgewählt.");
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Essensplan KW " . $weekPlan['week_number']; ?></title>
    <link rel="stylesheet" href="/essensplan/assets/print.css"> <!-- Pfad zur print.css -->
</head>
<body>
<main>
    <h2>Essensplan KW <?php echo $weekPlan['week_number']; ?> (<?php echo $firstDayOfWeek; ?>)</h2>

    <?php
    // Mahlzeitenplan abrufen
    $stmt = $conn->prepare("
        SELECT er.day_of_week, mc.name AS meal_category, r.title AS recipe_title
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
        $daysOfWeek = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
        $mealCategories = ['Frühstück', 'Znüni', 'Mittagessen', 'Zvieri', 'Abendessen'];
        
        echo "<table class='meal-plan-table'>"; // CSS-Klasse für Styling
        echo "<thead><tr><th></th>"; // Leere Zelle für die Kategorien-Spalte
        foreach ($daysOfWeek as $day) {
            echo "<th>$day</th>";
        }
        echo "</tr></thead><tbody>";

        foreach ($mealCategories as $category) {
            echo "<tr><td class='category'>$category</td>"; // Kategorien-Spalte
            foreach ($daysOfWeek as $day) {
                $recipe = '';
                foreach ($mealPlan as $meal) {
                    if ($meal['day_of_week'] == $day && $meal['meal_category'] == $category) {
                        $recipe = $meal['recipe_title'];
                        break;
                    }
                }
                echo "<td>" . ($recipe ? $recipe : '-') . "</td>"; // Rezept anzeigen oder '-' wenn leer
            }
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>Keine Mahlzeitenzuordnungen für diesen Wochenplan gefunden.</p>";
    }
    ?>
</main>
<script>
    window.onload = function() {
        window.print(); // Automatischer Druckstart
    }
</script>
</body>
</html>
