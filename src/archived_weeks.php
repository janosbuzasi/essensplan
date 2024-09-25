<?php
$title = "Archivierte Essenspläne";
require_once 'auth.php'; // Überprüfung der Benutzeranmeldung (auth.php einbinden)
require '../header.php'; // Header einfügen
?>
<main>
    <h2><?php echo $title; ?></h2>
    <p>Hier siehst du eine Übersicht aller archivierten Essenspläne.</p>
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
            require_once '../config/db.php';
            $db = new Database();
            $conn = $db->getConnection();

            $stmt = $conn->query("SELECT * FROM essensplan WHERE status = 'archiviert' ORDER BY year DESC, week_number DESC");
            $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($plans)) {
                foreach ($plans as $plan) {
                    echo "<tr>";
                    echo "<td>Woche " . $plan['week_number'] . "</td>";
                    echo "<td>" . $plan['year'] . "</td>";
                    echo "<td>
                        <a href='view_week.php?id=" . $plan['id'] . "' class='btn btn-view'>Ansehen</a> 
                        <a href='edit_week.php?id=" . $plan['id'] . "' class='btn btn-edit'>Bearbeiten</a> 
                        <a href='delete_week.php?id=" . $plan['id'] . "' class='btn btn-delete' onclick=\"return confirm('Möchtest du diesen Essensplan wirklich löschen?');\">Löschen</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>Keine archivierten Essenspläne vorhanden.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>
<?php
include '../footer.php'; // Footer einfügen
?>
