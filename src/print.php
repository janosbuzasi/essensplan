<?php
$title = "Essensplan drucken";
require '../header.php'; // Header einfügen

// Datenbankverbindung einbinden
require_once '../config/db.php';
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
        // Startdatum der Woche berechnen
        $weekNumber = $weekPlan['week_number'];
        $year = $weekPlan['year'];
        $mondayDate = new DateTime();
        $mondayDate->setISODate($year, $weekNumber); // Setzt das Datum auf den Montag der Woche

        // Enddatum (Sonntag) berechnen
        $sundayDate = clone $mondayDate;
        $sundayDate->modify('+6 days'); // Sonntag ist 6 Tage nach Montag

        // Titel mit Zeitraum anzeigen
        echo "<h2>Essensplan KW {$weekNumber} ({$mondayDate->format('d.m.Y')} - {$sundayDate->format('d.m.Y')})</h2>";

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
            // Array für die Zuordnung von Wochentagen und Daten
            $daysOfWeek = [
                'Montag' => $mondayDate,
                'Dienstag' => (clone $mondayDate)->modify('+1 day'),
                'Mittwoch' => (clone $mondayDate)->modify('+2 days'),
                'Donnerstag' => (clone $mondayDate)->modify('+3 days'),
                'Freitag' => (clone $mondayDate)->modify('+4 days'),
                'Samstag' => (clone $mondayDate)->modify('+5 days'),
                'Sonntag' => $sundayDate
            ];

            echo "<table class='styled-table'>"; // CSS-Klasse für Styling
            echo "<thead><tr>";

            // Tabelle mit Tagen und Datumskopfzeile
            foreach ($daysOfWeek as $day => $date) {
                echo "<th>{$day}<br>{$date->format('d.m.Y')}</th>";
            }

            echo "</tr></thead><tbody>";

            // Leere Array für die Tagesdaten
            $dayData = array_fill_keys(array_keys($daysOfWeek), '');

            // Daten für jeden Tag aufbereiten
            foreach ($mealPlan as $meal) {
                $day = $meal['day_of_week'];
                $mealCategory = $meal['meal_category'];
                $recipeTitle = $meal['recipe_title'];
                $dayData[$day] .= "<strong>{$mealCategory}:</strong> {$recipeTitle}<br>";
            }

            // Ausgabe der Mahlzeiten pro Tag in die Tabelle
            echo "<tr>";
            foreach ($dayData as $meals) {
                echo "<td>{$meals}</td>";
            }
            echo "</tr>";

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
<a href="view_weeks.php" class="btn btn-view">Zurück zur Übersicht</a>

<?php
include '../footer.php'; // Footer einfügen
?>
