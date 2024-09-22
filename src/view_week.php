<?php
$title = "Wochenplan anzeigen";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Wochenplan abrufen
        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $weekPlan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($weekPlan) {
            echo "<h3>Wochenplan: Woche " . $weekPlan['week_number'] . " im Jahr " . $weekPlan['year'] . "</h3>";
            echo "<p><strong>Name:</strong> " . $weekPlan['week_name'] . "</p>";
            echo "<p><strong>Beschreibung:</strong> " . $weekPlan['description'] . "</p>";

            // Mahlzeiten für den Wochenplan abrufen
            $stmt = $conn->prepare("
                SELECT er.day_of_week, mc.name AS meal_category, r.title AS recipe_title 
                FROM essensplan_recipes er
                JOIN recipes r ON er.recipe_id = r.id
                JOIN meal_categories mc ON er.meal_category_id = mc.id
                WHERE er.essensplan_id = ?
                ORDER BY er.day_of_week, mc.name
            ");
            $stmt->execute([$weekPlan['id']]);
            $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($meals) {
                echo "<h3>Mahlzeitenplan</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Tag</th><th>Kategorie</th><th>Rezept</th></tr>";
                foreach ($meals as $meal) {
                    echo "<tr>
                            <td>" . $meal['day_of_week'] . "</td>
                            <td>" . $meal['meal_category'] . "</td>
                            <td>" . $meal['recipe_title'] . "</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Keine Mahlzeiten für diesen Wochenplan gefunden.</p>";
            }
        } else {
            echo "<p>Wochenplan nicht gefunden.</p>";
        }
    } else {
        echo "<p>Kein Wochenplan ausgewählt.</p>";
    }
    ?>
</main>
<?php
include '../footer.php';
?>
