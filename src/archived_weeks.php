<?php
$title = "Archivierte Essenspläne"; 
require '../header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier werden alle archivierten Essenspläne angezeigt:</p>
    <ul>
        <?php
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Abruf der archivierten Essenspläne
        $stmt = $conn->query("SELECT * FROM essensplan WHERE status = 'archiviert' ORDER BY year, week_number");
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($plans)) {
            foreach ($plans as $plan) {
                echo "<li>Woche " . $plan['week_number'] . " im Jahr " . $plan['year'] . " - <a href='view_week.php?id=" . $plan['id'] . "'>Ansehen</a> | <a href='edit_week.php?id=" . $plan['id'] . "'>Bearbeiten</a> | <a href='delete_week.php?id=" . $plan['id'] . "' onclick=\"return confirm('Möchtest du diesen Essensplan wirklich löschen?');\">Löschen</a></li>";
            }
        } else {
            echo "<li>Keine archivierten Essenspläne vorhanden.</li>";
        }
        ?>
    </ul>
</main>
<?php
include '../footer.php';
?>
