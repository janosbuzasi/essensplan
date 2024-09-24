<?php
$title = "Essensplan"; // Standard-Titel
require '../config/db.php';
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
        $weekNumber = $weekPlan['week_number'];
        $year = $weekPlan['year'];
        
        // Startdatum und Enddatum der Kalenderwoche berechnen
        $dto = new DateTime();
        $dto->setISODate($year, $weekNumber);
        $startDate = $dto->format('d.m.Y');
        $dto->modify('+6 days');
        $endDate = $dto->format('d.m.Y');
        
        // Angepasster Titel
        $title = "Essensplan KW $weekNumber ($startDate - $endDate)";
        
        // Mahlzeitenplan-Daten abrufen
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
    } else {
        echo "Wochenplan nicht gefunden.";
        exit;
    }
} else {
    echo "Kein Wochenplan ausgewählt.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="/essensplan/assets/print.css"> <!-- Pfad zur print.css -->
</head>
<body>
<main>
    <h2><?php echo $title; ?></h2>

    <?php if ($mealPlan): ?>
        <table class="meal-plan-table">
            <thead>
                <tr>
                    <?php
                    // Berechnung der Tagesdaten und Anzeige im Kopf der Tabelle
                    $dto->setISODate($year, $weekNumber); // Zurücksetzen auf Wochenbeginn
                    $daysOfWeek = ['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'];
                    foreach ($daysOfWeek as $day) {
                        $date = $dto->format('d.m.Y');
                        echo "<th>$day<br>$date</th>";
                        $dto->modify('+1 day');
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Mahlzeiten für jeden Tag anzeigen
                foreach ($daysOfWeek as $day) {
                    echo "<tr>";
                    foreach ($mealPlan as $meal) {
                        if ($meal['day_of_week'] == $day) {
                            echo "<td class='category'>" . $meal['meal_category'] . ": " . $meal['recipe_title'] . "</td>";
                        }
                    }
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Keine Mahlzeitenzuordnungen für diesen Wochenplan gefunden.</p>
    <?php endif; ?>
</main>
</body>
</html>
