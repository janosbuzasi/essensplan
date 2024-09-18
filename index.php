
<?php
require_once 'config/db.php';  // Korrigierter Pfad zur Datenbankverbindung

$db = new Database();
$conn = $db->getConnection();

// Überprüfen, ob es Wochenpläne gibt und die Anzahl ausgeben
$stmt = $conn->query("SELECT COUNT(*) AS count FROM week_plan");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Anzahl der vorhandenen Wochenpläne: " . $row['count'] . "<br>";

// Überprüfen, ob die Tabelle 'week_plan' Daten enthält
$stmt = $conn->query("SELECT * FROM week_plan");
$weekPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($weekPlans)) {
    echo "Die Tabelle 'week_plan' ist leer.";
} else {
    echo "Daten gefunden: <pre>";
    print_r($weekPlans);
    echo "</pre>";
}
?>

<h1>Wochenplan für Essen</h1>

<!-- Wochenplan erstellen -->
<h2>Neuen Wochenplan erstellen</h2>
<form method="POST">
    <label for="week_number">Kalenderwoche:</label>
    <select name="week_number">
        <?php for ($i = 1; $i <= 52; $i++): ?>
            <option value="<?php echo $i; ?>">
                Woche <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select>

    <label for="year">Jahr:</label>
    <input type="number" name="year" value="<?php echo date('Y'); ?>" required>

    <input type="submit" name="create_week" value="Wochenplan erstellen">
</form>

<!-- Vorhandene Wochenpläne anzeigen -->
<h2>Vorhandene Wochenpläne</h2>
<ul>
    <?php foreach ($weekPlans as $plan): ?>
        <li>
            Woche <?php echo $plan['week_number']; ?> (Jahr <?php echo $plan['year']; ?>) - 
            <a href="src/view_week.php?week_plan_id=<?php echo $plan['id']; ?>">Ansehen</a> | 
            <a href="src/edit_week.php?week_plan_id=<?php echo $plan['id']; ?>">Bearbeiten</a> | 
            <a href="src/delete_week.php?week_plan_id=<?php echo $plan['id']; ?>" onclick="return confirm('Möchtest du diesen Wochenplan wirklich löschen?');">Löschen</a>
        </li>
    <?php endforeach; ?>
</ul>
