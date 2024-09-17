<?php
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

// Alle Rezepte abrufen
$stmt = $conn->query("SELECT * FROM recipes ORDER BY title");
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Rezepte</h1>

<a href="add_recipe.php">Neues Rezept hinzufügen</a>

<table border="1">
    <tr>
        <th>Titel</th>
        <th>Kategorie</th>
        <th>Aktionen</th>
    </tr>
    <?php foreach ($recipes as $recipe): ?>
        <tr>
            <td><?php echo $recipe['title']; ?></td>
            <td><?php echo $recipe['category']; ?></td>
            <td>
                <a href="edit_recipe.php?recipe_id=<?php echo $recipe['id']; ?>">Bearbeiten</a> | 
                <a href="delete_recipe.php?recipe_id=<?php echo $recipe['id']; ?>" onclick="return confirm('Möchtest du dieses Rezept wirklich löschen?');">Löschen</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
