<?php
$title = "Wochenpläne verwalten";
require '../header.php';
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php
    require_once '../config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Vorhandene Wochenpläne abrufen
    $stmt = $conn->query("SELECT * FROM essensplan ORDER BY year DESC, week_number DESC");
    $weekPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($weekPlans) {
        echo "<table border='1'>";
        echo "<tr><th>Woche</th><th>Jahr</th><th>Beschreibung</th><th>Status</th><th>Aktionen</th></tr>";
        foreach ($weekPlans as $plan) {
            echo "<tr>";
            echo "<td>Woche " . $plan['week_number'] . "</td>";
            echo "<td>" . $plan['year'] . "</td>";
            echo "<td>" . $plan['description'] . "</td>";
            echo "<td>" . $plan['status'] . "</td>";
            echo "<td>";
            echo "<a href='view_week.php?id=" . $plan['id'] . "'>Ansehen</a> | ";
            echo "<a href='edit_week.php?id=" . $plan['id'] . "'>Bearbeiten</a> | ";
            echo "<a href='delete_week.php?id=" . $plan['id'] . "' onclick=\"return confirm('Möchtest du diesen Wochenplan wirklich löschen?');\">Löschen</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Keine Wochenpläne gefunden.</p>";
    }
    ?>
</main>
<?php
include '../footer.php';
?>
