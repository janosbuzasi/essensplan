<?php
require_once '../config/db.php';  // Pfad zur Datenbankverbindung

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

// Mahlzeiten für diese Woche abrufen
$query = "
    SELECT meal_plan.day_of_week, meal_plan.meal_type, recipes.title 
    FROM meal_plan 
    INNER JOIN recipes ON meal_plan.recipe_id = recipes.id 
    WHERE meal_plan.week_plan_id = ?
    ORDER BY FIELD(day_of_week, 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
";
$stmt = $conn->prepare($query);
$stmt->execute([$week_plan_id]);
$meal_plan = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wochenplan - Woche <?php echo $weekPlan['week_number']; ?> im Jahr <?php echo $weekPlan['year']; ?></title>
    <link rel="stylesheet" href="../assets/style.css"> <!-- Optionales CSS -->
</head>
<body>
    <h1>Wochenplan für Woche <?php echo $weekPlan['week_number']; ?> (Jahr <?php echo $weekPlan['year']; ?>)</h1>

    <?php if (empty($meal_plan)): ?>
        <p>Keine Mahlzeiten für diese Woche gefunden.</p>
    <?php else: ?>
        <table border="1">
            <tr>
                <th>Tag</th>
                <th>Mahlzeit</th>
                <th>Rezept</th>
            </tr>
            <?php foreach ($meal_plan as $meal): ?>
                <tr>
                    <td><?php echo $meal['day_of_week']; ?></td>
                    <td><?php echo $meal['meal_type']; ?></td>
                    <td><?php echo $meal['title']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <a href="../index.php">Zurück zum Wochenplan</a>
</body>
</html>
