<?php
$title = "Mahlzeitenkategorien verwalten";
require '../header.php'; // Header einfügen
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

// Kategorien aus der Tabelle meal_categories abrufen
$stmt = $conn->query("SELECT * FROM meal_categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<main>
    <h2><?php echo $title; ?></h2>
    <a href="add_category.php" class="btn btn-add">Neue Kategorie hinzufügen</a> <!-- Button zum Hinzufügen neuer Kategorien -->

    <?php if ($categories): ?>
        <table class="styled-table"> <!-- Nutzung der bestehenden CSS-Klassen -->
            <thead>
                <tr>
                    <th>Mahlzeitenkategorie</th>
                    <th>Beschreibung</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cat['name'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($cat['description'], ENT_QUOTES); ?></td>
                        <td>
                            <a href="edit_category.php?category=<?php echo urlencode($cat['name']); ?>" class="btn btn-edit">Bearbeiten</a>
                            <a href="delete_category.php?category=<?php echo urlencode($cat['name']); ?>" class="btn btn-delete" onclick="return confirm('Möchtest du diese Kategorie wirklich löschen?');">Löschen</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Keine Kategorien gefunden.</p>
    <?php endif; ?>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
