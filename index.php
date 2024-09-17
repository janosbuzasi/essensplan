<?php
require_once 'config/db.php';  // Korrigierter Pfad zur Datenbankverbindung

$db = new Database();
$conn = $db->getConnection();

// Vorhandene Wochenpläne anzeigen
$weekPlans = $conn->query("SELECT * FROM week_plan ORDER BY year, week_number")->fetchAll(PDO::FETCH_ASSOC);

// Debug-Ausgabe der Wochenpläne
echo "<pre>";
print_r($weekPlans);  // Gibt die abgerufenen Wochenpläne aus
echo "</pre>";
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
