<?php
$title = "Wochenpläne verwalten";
require '../header.php'; // Header einfügen
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier kannst du die bestehenden Wochenpläne anzeigen, bearbeiten oder löschen.</p>

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
        
        // Schleife über die abgerufenen Wochenpläne
        foreach ($weekPlans as $plan) {
            echo "<tr>";
            echo "<td>Woche " . $plan['week_number'] . "</td>";
            echo "<td>" . $plan['year'] . "</td>";
            echo "<td>" . htmlspecialchars($plan['description'], ENT_QUOTES) . "</td>";
            echo "<td>" . $plan['status'] . "</td>";
            echo "<td>";
            echo "<a href='view_week.php?id=" . $plan['id'] . "' class='btn btn-view'>Ansehen</a> ";
            echo "<a href='edit_week.php?id=" . $plan['id'] . "' class='btn btn-edit'>Bearbeiten</a> ";
            echo "<a href='delete_week.php?id=" . $plan['id'] . "' class='btn btn-delete' onclick=\"return confirm('Möchtest du diesen Wochenplan wirklich löschen?');\">Löschen</a> ";
            
            // Überprüfen, ob es Zuweisungen gibt, um die richtige Aktion anzuzeigen
            $stmt2 = $conn->prepare("SELECT COUNT(*) FROM meal_plan WHERE week_plan_id = ?");
            $stmt2->execute([$plan['id']]);
            $hasAssignments = $stmt2->fetchColumn();
            if ($hasAssignments) {
                echo "<a href='edit_assignment.php?week_plan_id=" . $plan['id'] . "' class='btn btn-edit'>Zuweisungen bearbeiten</a>";
            } else {
                echo "<a href='assign_recipe_to_week.php?week_plan_id=" . $plan['id'] . "' class='btn btn-add'>Rezepte zuweisen</a>";
            }
            echo "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>Keine Wochenpläne gefunden.</p>";
    }
    ?>

    <!-- Link zum Hinzufügen eines neuen Wochenplans -->
    <a href="add_week.php" class="btn btn-add">Neuen Wochenplan hinzufügen</a>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
