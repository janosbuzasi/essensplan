<?php
$title = "Essenspläne Übersicht"; 
require 'header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier siehst du eine Übersicht aller aktiven und archivierten Essenspläne.</p>
    <h3>Aktive Essenspläne</h3>
    <ul>
        <?php
        require_once '../config/db.php';
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT * FROM essensplan WHERE status = 'aktiv' ORDER BY year, week_number");
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($plans)) {
            foreach ($plans as $plan) {
                echo "<li>Woche " . $plan['week_number'] . " im Jahr " . $plan['year'] . " - <a href='view_week.php?id=" . $plan['id'] . "'>Ansehen</a> | <a href='edit_week.php?id=" . $plan['id'] . "'>Bearbeiten</a> | <a href='archive_week.php?id=" . $plan['id'] . "'>Archivieren</a> | <a href='delete_week.php?id=" . $plan['id'] . "' onclick=\"return confirm('Möchtest du diesen Essensplan wirklich löschen?');\">Löschen</a></li>";
            }
        } else {
            echo "<li>Keine aktiven Essenspläne vorhanden.</li>";
        }
        ?>
    </ul>
    <h3>Archivierte Essenspläne</h3>
    <ul>
        <?php
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
    <a href="add_week.php">Neuen Essensplan hinzufügen</a>
</main>
<?php
include 'footer.php';
?>
