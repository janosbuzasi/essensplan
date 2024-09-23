<?php
$title = "Essenspläne Übersicht";
require 'header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier siehst du den nächsten verfügbaren Essensplan basierend auf dem aktuellen Datum.</p>

    <?php
    require_once 'config/db.php';
    $db = new Database();
    $conn = $db->getConnection();

    // Aktuelles Datum und Jahr
    $currentYear = date('Y');
    $currentWeek = date('W');

    // Nächsten verfügbaren Essensplan basierend auf dem aktuellen Datum abrufen
    $stmt = $conn->prepare("SELECT * FROM essensplan WHERE status = 'aktiv' AND (year > ? OR (year = ? AND week_number >= ?)) ORDER BY year, week_number LIMIT 1");
    $stmt->execute([$currentYear, $currentYear, $currentWeek]);
    $nextPlan = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nextPlan) {
        echo "<h3>Nächster verfügbarer Essensplan</h3>";
        echo "<p><strong>Woche " . $nextPlan['week_number'] . " im Jahr " . $nextPlan['year'] . "</strong></p>";
        echo "<p><a href='src/view_week.php?id=" . $nextPlan['id'] . "' class='btn btn-view'>Essensplan ansehen</a></p>";
    } else {
        echo "<p>Kein zukünftiger Essensplan verfügbar.</p>";
    }
    ?>

    <!-- Suchfunktion -->
    <h3>Essenspläne durchsuchen</h3>
    <form method="get" action="index.php">
        <input type="text" name="search" placeholder="Nach Woche oder Jahr suchen...">
        <input type="submit" value="Suchen" class="btn btn-add">
    </form>

    <!-- Aktive Essenspläne anzeigen -->
    <h3>Aktive Essenspläne</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Woche</th>
                <th>Jahr</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $searchQuery = "";
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = $_GET['search'];
                $searchQuery = "AND (week_number LIKE '%$search%' OR year LIKE '%$search%')";
            }
            $stmt = $conn->query("SELECT * FROM essensplan WHERE status = 'aktiv' $searchQuery ORDER BY year DESC, week_number DESC");
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($plans)) {
                foreach ($plans as $plan) {
                    echo "<tr>";
                    echo "<td>Woche " . $plan['week_number'] . "</td>";
                    echo "<td>" . $plan['year'] . "</td>";
                    echo "<td>
                        <a href='src/view_week.php?id=" . $plan['id'] . "' class='btn btn-view'>Ansehen</a> 
                        <a href='src/edit_week.php?id=" . $plan['id'] . "' class='btn btn-edit'>Bearbeiten</a> 
                        <a href='src/archive_week.php?id=" . $plan['id'] . "' class='btn btn-archive'>Archivieren</a> 
                        <a href='src/delete_week.php?id=" . $plan['id'] . "' class='btn btn-delete' onclick=\"return confirm('Möchtest du diesen Essensplan wirklich löschen?');\">Löschen</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Keine aktiven Essenspläne vorhanden.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>
<?php
include 'footer.php';
?>
