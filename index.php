
<?php
// Datenbankverbindung einbinden
require_once 'config/db.php';

$db = new Database();
$conn = $db->getConnection();

// Abfrage, um den Wochenplan anzuzeigen
$query = "
    SELECT meal_plan.day_of_week, recipes.title 
    FROM meal_plan 
    INNER JOIN recipes ON meal_plan.recipe_id = recipes.id 
    ORDER BY FIELD(day_of_week, 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');
";
$stmt = $conn->prepare($query);
$stmt->execute();

$meal_plan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wochenplan</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Wochenplan für Essen</h1>
    <table>
        <tr>
            <th>Tag</th>
            <th>Rezept</th>
        </tr>
        <?php foreach ($meal_plan as $meal): ?>
        <tr>
            <td><?php echo $meal['day_of_week']; ?></td>
            <td><?php echo $meal['title']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
