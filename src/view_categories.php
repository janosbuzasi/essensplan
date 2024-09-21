<?php
$title = "Kategorien anzeigen"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier werden alle vorhandenen Kategorien angezeigt:</p>
    <ul>
        <?php
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Abruf der vorhandenen Kategorien
        $stmt = $conn->query("SELECT * FROM categories ORDER BY name");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($categories)) {
            foreach ($categories as $category) {
                echo "<li>" . $category['name'] . " - <a href='edit_category.php?id=" . $category['id'] . "'>Bearbeiten</a> | <a href='delete_category.php?id=" . $category['id'] . "' onclick=\"return confirm('Möchtest du diese Kategorie wirklich löschen?');\">Löschen</a></li>";
            }
        } else {
            echo "<li>Keine Kategorien vorhanden.</li>";
        }
        ?>
    </ul>
    <a href="add_category.php">Neue Kategorie hinzufügen</a>
</main>
<?php
include '../footer.php';
?>
