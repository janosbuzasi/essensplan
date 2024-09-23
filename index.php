<?php
$title = "Essenspläne Übersicht";
require 'header.php';  // Inkludiere den Header
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier siehst du eine Übersicht aller aktiven und archivierten Essenspläne. Du kannst neue Essenspläne hinzufügen, bestehende bearbeiten oder archivieren.</p>

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
            $stmt = $conn->query("SELECT * FROM essensplan WHERE status = 'aktiv' ORDER BY year DESC, week_number DESC");
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
            $stmt = $conn->query("SELECT * FROM essensplan WHERE status = 'archiviert' ORDER BY year DESC, week_number DESC");
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

<style>
    /* Einfache CSS-Stile für die Buttons */
    .btn {
        display: inline-block;
        padding: 5px 10px;
        margin: 2px;
        text-decoration: none;
        border-radius: 5px;
        color: #fff;
    }

    .btn-view {
        background-color: #5cb85c;
    }

    .btn-edit {
        background-color: #5bc0de;
    }

    .btn-archive {
        background-color: #f0ad4e;
    }

    .btn-delete {
        background-color: #d9534f;
    }

    .btn-add {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 15px;
        background-color: #337ab7;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, table td {
        padding: 10px;
        text-align: left;
    }

    table th {
        background-color: #f8f8f8;
    }
</style>
