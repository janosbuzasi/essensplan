<?php
$title = "Essenspläne Übersicht"; 
require 'header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier siehst du eine Übersicht aller aktiven und archivierten Essenspläne.</p>
    <h3>Aktive Essenspläne</h3>
    <table class="styled-table">
        <thead>
            <tr>
                <th>Woche</th>
                <th>Jahr</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once '../essensplan/config/db.php';
            $db = new Database();
            $conn = $db->getConnection();
            $stmt = $conn->query("SELECT * FROM essensplan WHERE status = 'aktiv' ORDER BY year, week_number");
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($plans)) {
                foreach ($plans as $plan) {
                    echo "<tr>";
                    echo "<td>Woche " . $plan['week_number'] . "</td>";
                    echo "<td>" . $plan['year'] . "</td>";
                    echo "<td>";
                    echo "<a href='src/view_week.php?id=" . $plan['id'] . "' class='btn btn-view' title='Essensplan ansehen'><i class='fas fa-eye'></i></a>";
                    echo "<a href='src/edit_week.php?id=" . $plan['id'] . "' class='btn btn-edit' title='Essensplan bearbeiten'><i class='fas fa-edit'></i></a>";
                    echo "<a href='src/archive_week.php?id=" . $plan['id'] . "' class='btn btn-archive' title='Essensplan archivieren'><i class='fas fa-archive'></i></a>";
                    echo "<a href='src/delete_week.php?id=" . $plan['id'] . "' class='btn btn-delete' title='Essensplan löschen' onclick=\"return confirm('Möchtest du diesen Essensplan wirklich löschen?');\"><i class='fas fa-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Keine aktiven Essenspläne vorhanden.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="src/add_week.php" class="btn btn-add">Neuen Essensplan hinzufügen</a>
</main>
<?php
include 'footer.php';
?>
