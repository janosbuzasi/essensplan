<?php
require_once '../config/db.php'; // Verbindung zur Datenbank herstellen

$db = new Database();
$conn = $db->getConnection();

if (!isset($_GET['week_plan_id'])) {
    echo "Wochenplan-ID nicht angegeben.";
    exit;
}

$week_plan_id = $_GET['week_plan_id'];

// Wochenplan abrufen
$stmt = $conn->prepare("SELECT * FROM week_plan WHERE id = ?");
$stmt->execute([$week_plan_id]);
$weekPlan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$weekPlan) {
    echo "Wochenplan nicht gefunden.";
    exit;
}

// Alle Rezepte abrufen
$allRecipes = $conn->query("SELECT * FROM recipes")->fetchAll(PDO::FETCH_ASSOC);

// Mahlzeiten abrufen
$mealPlan = $conn->prepare("
    SELECT meal_plan.id, meal_plan.day_of_week, meal_plan.meal_type, recipes.title 
    FROM meal_plan 
    INNER JOIN recipes ON meal_plan.recipe_id = recipes.id 
    WHERE meal_plan.week_plan_id = ?
    ORDER BY FIELD(meal_plan.day_of_week, 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag')
");
$mealPlan->execute([$week_plan_id]);
$meals = $mealPlan->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Daten aktualisieren
    foreach ($_POST['meals'] as $meal_id => $new_recipe_id) {
        $updateStmt = $conn->prepare("UPDATE meal_plan SET recipe_id = ? WHERE id = ?");
        $updateStmt->execute([$new_recipe_id, $meal_id]);
    }
    echo "Wochenplan erfolgreich aktualisiert!";
    // Optional: Seite neu laden, um die aktualisierten Daten anzuzeigen
    header("Location: edit_week.php?week_plan_id=" . $week_plan_id);
    exit;
}
?>

<h1>Wochenplan bearbeiten - Woche <?php echo $weekPlan['week_number']; ?></h1>

<?php if (empty($meals)): ?>
    <p>Keine Mahlzeiten vorhanden.</p>
<?php else: ?>
    <form method="POST">
        <input type="hidden" name="week_plan_id" value="<?php echo $week_plan_id; ?>">

        <?php foreach ($meals as $meal): ?>
            <div>
                <label><?php echo $meal['day_of_week']; ?> - <?php echo $meal['meal_type']; ?>:</label>
                <select name="meals[<?php echo $meal['id']; ?>]">
                    <?php foreach ($allRecipes as $recipe): ?>
                        <option value="<?php echo $recipe['id']; ?>" <?php if ($recipe['title'] == $meal['title']) echo 'selected'; ?>>
                            <?php echo $recipe['title']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>

        <input type="submit" value="Wochenplan aktualisieren">
    </form>
<?php endif; ?>
<a href="../index.php">Zurück zum Wochenplan</a>
