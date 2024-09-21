<?php
$title = "Essensplan"; 
require 'header.php';  // Inkludiere den Header
?>
<!-- Body-Bereich -->
<main>
    <h2><?php echo $title; ?></h2>
    <p>Willkommen im Essensplan-Verwaltungssystem. Hier kannst du deine wöchentlichen Essenspläne verwalten, Rezepte hinzufügen und anpassen.</p>

    <!-- Beispiel: Anzeige der vorhandenen Essenspläne -->
    <h3>Vorhandene Essenspläne</h3>
    <ul>
        <?php
        // Verbindung zur Datenbank herstellen
        require_once 'config/db.php';
        $db = new Database();
        $conn = $db->getConnection();

        // Abruf der vorhandenen Essenspläne
        $stmt = $conn->query("SELECT * FROM essensplan ORDER BY year, week_number");
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($plans)) {
            foreach ($plans as $plan) {
                echo "<li>Woche " . $plan['week_number'] . " im Jahr " . $plan['year'] . " - " . $plan['week_name'] . "</li>";
            }
        } else {
            echo "<li>Keine Essenspläne vorhanden.</li>";
        }
        ?>
    </ul>
</main>

<?php
include 'footer.php';
?>
