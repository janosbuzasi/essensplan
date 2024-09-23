<?php
$title = "Essenspläne Übersicht";
require 'header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier siehst du eine Übersicht aller aktiven und archivierten Essenspläne. Du kannst neue Essenspläne hinzufügen, bestehende bearbeiten oder archivieren.</p>

    <!-- Button für Dark Mode -->
    <button onclick="toggleDarkMode()" class="btn btn-add">Dark Mode umschalten</button>

    <!-- Suchfunktion -->
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
            require_once 'config/db.php';
            $db = new Database();
            $conn = $db->getConnection();
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

    <!-- Archivierte Essenspläne anzeigen -->
    <h3>Archivierte Essenspläne</h3>
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
            $stmt = $conn->query("SELECT * FROM essensplan WHERE status = 'archiviert' $searchQuery ORDER BY year DESC, week_number DESC");
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($plans)) {
                foreach ($plans as $plan) {
                    echo "<tr>";
                    echo "<td>Woche " . $plan['week_number'] . "</td>";
                    echo "<td>" . $plan['year'] . "</td>";
                    echo "<td>
                        <a href='src/view_week.php?id=" . $plan['id'] . "' class='btn btn-view'>Ansehen</a> 
                        <a href='src/edit_week.php?id=" . $plan['id'] . "' class='btn btn-edit'>Bearbeiten</a> 
                        <a href='src/delete_week.php?id=" . $plan['id'] . "' class='btn btn-delete' onclick=\"return confirm('Möchtest du diesen Essensplan wirklich löschen?');\">Löschen</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Keine archivierten Essenspläne vorhanden.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="src/add_week.php" class="btn btn-add">Neuen Essensplan hinzufügen</a>
</main>
<?php
include 'footer.php';
?>

<script>
// Funktion zum Umschalten des Dark Mode
function toggleDarkMode() {
    var element = document.body;
    element.classList.toggle("dark-mode");
}
</script>
