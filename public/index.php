<?php
require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->query("SELECT * FROM meal_plan INNER JOIN recipes ON meal_plan.recipe_id = recipes.id");
$meal_plan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
