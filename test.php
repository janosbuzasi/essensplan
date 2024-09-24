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
        <!-- Beispiel für einen Tabellenzeile mit Icons für Aktionen -->
<td>
    <a href='view_week.php?id=<?php echo $plan['id']; ?>' class='btn btn-view'><i class='fas fa-eye'></i></a> <!-- Auge-Icon für Ansehen -->
    <a href='edit_week.php?id=<?php echo $plan['id']; ?>' class='btn btn-edit'><i class='fas fa-edit'></i></a> <!-- Stift-Icon für Bearbeiten -->
    <a href='archive_week.php?id=<?php echo $plan['id']; ?>' class='btn btn-archive'><i class='fas fa-archive'></i></a> <!-- Archiv-Icon für Archivieren -->
    <a href='delete_week.php?id=<?php echo $plan['id']; ?>' class='btn btn-delete' onclick="return confirm('Möchtest du diesen Essensplan wirklich löschen?');"><i class='fas fa-trash'></i></a> <!-- Papierkorb-Icon für Löschen -->
</td>
        <tbody>
            <?php
            require_once '../config/db.php';
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
                    echo "<a href='view_week.php?id=" . $plan['id'] . "' class='btn btn-view'><i class='fas fa-eye'></i></a>";
                    echo "<a href='edit_week.php?id=" . $plan['id'] . "' class='btn btn-edit'><i class='fas fa-edit'></i></a>";
                    echo "<a href='archive_week.php?id=" . $plan['id'] . "' class='btn btn-archive'><i class='fas fa-archive'></i></a>";
                    echo "<a href='delete_week.php?id=" . $plan['id'] . "' class='btn btn-delete' onclick=\"return confirm('Möchtest du diesen Essensplan wirklich löschen?');\"><i class='fas fa-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Keine aktiven Essenspläne vorhanden.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="add_week.php" class="btn btn-add">Neuen Essensplan hinzufügen</a>
</main>
<?php
include 'footer.php';
?>
