
<?php
require_once '../config/db.php';  // Pfad anpassen

$db = new Database();
$conn = $db->getConnection();

$week_plan_id = $_GET['week_plan_id'];

// Mahlzeit hinzufügen
if ($_POST && isset($_POST['add_meal'])) {
    $recipe_id = $_POST['recipe_id'];
    $day_of_week = $_POST['day_of_week'];
    $meal_type = $_POST['meal_type'];

    $stmt = $conn->prepare("INSERT INTO meal_plan (week_plan_id, recipe_id, day_of_week, meal_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$week_plan_id, $recipe_id, $day_of_week, $meal_type]);

    echo "Mahlzeit erfolgreich hinzugefügt!";
}

// Rezepte abrufen
$stmt = $conn->query("SELECT id, title FROM recipes");
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Wochenplan verwalten</h1>

<!-- Mahlzeit hinzufügen -->
<form method="POST">
    <label for="recipe_id">Rezept:</label>
    <select name="recipe_id">
        <?php foreach ($recipes as $recipe): ?>
            <option value="<?php echo $recipe['id']; ?>"><?php echo $recipe['title']; ?></option>
        <?php endforeach; ?>
    </select>

    <label for="day_of_week">Tag:</label>
    <select name="day_of_week">
        <option value="Montag">Montag</option>
        <option value="Dienstag">Dienstag</option>
        <option value="Mittwoch">Mittwoch</option>
        <option value="Donnerstag">Donnerstag</option>
        <option value="Freitag">Freitag</option>
        <option value="Samstag">Samstag</option>
        <option value="Sonntag">Sonntag</option>
    </select>

    <label for="meal_type">Mahlzeit:</label>
    <select name="meal_type">
        <option value="Frühstück">Frühstück</option>
        <option value="Mittagessen">Mittagessen</option>
        <option value="Abendessen">Abendessen</option>
    </select>

    <input type="submit" name="add_meal" value="Mahlzeit hinzufügen">
</form>
