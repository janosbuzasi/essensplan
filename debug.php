<?php
require_once '../config/db.php'; // Pfad zur Datenbankverbindung sicherstellen
$db = new Database();
$conn = $db->getConnection();

// Prüfen, ob die Verbindung erfolgreich ist
if (!$conn) {
    die("Datenbankverbindung fehlgeschlagen!");
}

// Daten abrufen
$stmt = $conn->query("SELECT * FROM essensplan ORDER BY year DESC, week_number DESC");
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Anzahl der abgerufenen Pläne anzeigen
echo "Anzahl der gefundenen Essenspläne: " . count($plans);

// Überprüfen, ob Daten vorhanden sind
if ($plans) {
    foreach ($plans as $plan) {
        echo "<p>Plan: Woche " . $plan['week_number'] . " im Jahr " . $plan['year'] . "</p>";
    }
} else {
    echo "<p>Keine Essenspläne gefunden.</p>";
}
?>
