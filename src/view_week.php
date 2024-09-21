<?php
$title = "Essensplan Details"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    if (isset($_GET['id'])) {
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Abruf des spezifischen Essensplans
        $stmt = $conn->prepare("SELECT * FROM essensplan WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($plan) {
            echo "<h3>Essensplan: Woche " . $plan['week_number'] . " im Jahr " . $plan['year'] . "</h3>";
            echo "<p>Beschreibung: " . $plan['description'] . "</p>";

            // Rezepte anzeigen, die diesem Essensplan zugeordnet sind
            echo "<h4>Zugeordnete Rezepte:</h4>";
            $stmt = $conn->prepare("SELECT er.*, r.title FROM essensplan_recipes er JOIN recipes r ON er.recipe_id = r.id WHERE er.essensplan_id = ?");
            $stmt->execute([$_GET['id']]);
            $recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($recipes)) {
                echo "<ul>";
                foreach ($recipes as $recipe) {
                    echo "<li>" . $recipe['day_of_week'] . " - " . $recipe['meal_type'] . ": " . $recipe['title'] . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>Keine Rezepte zugeordnet.</p>";
            }
            echo "<a href='assign_recipe_to_week.php?id=" . $plan['id'] . "'>Rezept zuordnen</a>";
        } else {
            echo "<p>Essensplan nicht gefunden.</p>";
        }
    } else {
        echo "<p>Kein Essensplan ausgewählt.</p>";
    }
    ?>
</main>
<?php
include '../footer.php';
?>
