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
        echo "<table class='styled-table'>"; // CSS-Klasse für Styling
        echo "<thead><tr><th>Woche</th><th>Jahr</th><th>Beschreibung</th><th>Status</th><th>Aktionen</th></tr></thead><tbody>";
        foreach ($weekPlans as $plan) {
            echo "<tr>";
            echo "<td>Woche " . $plan['week_number'] . "</td>";
            echo "<td>" . $plan['year'] . "</td>";
            echo "<td>" . $plan['description'] . "</td>";
            echo "<td>" . ucfirst($plan['status']) . "</td>"; // Status mit erstem Buchstaben groß
            echo "<td>";
            echo "<a href='view_week.php?id=" . $plan['id'] . "' class='btn btn-view'>Ansehen</a> ";
            echo "<a href='edit_week.php?id=" . $plan['id'] . "' class='btn btn-edit'>Bearbeiten</a> ";
            echo "<a href='delete_week.php?id=" . $plan['id'] . "' class='btn btn-delete' onclick=\"return confirm('Möchtest du diesen Wochenplan wirklich löschen?');\">Löschen</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Keine Wochenpläne gefunden.</p>";
    }
    ?>

    <!-- Link zum Hinzufügen eines neuen Wochenplans -->
    <a href="add_week.php" class="btn btn-add">Neuen Essensplan hinzufügen</a>
</main>
<?php
include '../footer.php';
?>
