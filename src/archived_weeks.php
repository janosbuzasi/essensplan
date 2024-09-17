<?php
require_once '../config/db.php';  // Verbindung zur Datenbank herstellen

$db = new Database();
$conn = $db->getConnection();

// Archivierte Wochenpläne abrufen
$query = "SELECT * FROM week_plan WHERE archived = 1 ORDER BY year, week_number";
$stmt = $conn->query($query);
$archivedWeeks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Archivierte Wochenpläne</h1>

<ul>
    <?php if (empty($archivedWeeks)): ?>
        <li>Keine archivierten Wochenpläne gefunden.</li>
    <?php else: ?>
        <?php foreach ($archivedWeeks as $week): ?>
            <li>
                Woche <?php echo $week['week_number']; ?> (Jahr <?php echo $week['year']; ?>) - 
                <a href="view_week.php?week_plan_id=<?php echo $week['id']; ?>">Ansehen</a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>

<a href="../index.php">Zurück zum Wochenplan</a>
