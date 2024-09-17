<?php
require_once 'config/db.php';  // Korrigierter Pfad zur Datenbankverbindung

$db = new Database();
$conn = $db->getConnection();

// Neues Wochenplan-Handling
if ($_POST && isset($_POST['create_week'])) {
    $week_number = $_POST['week_number'];
    $year = $_POST['year'];

    // Prüfen, ob der Wochenplan schon existiert
    $stmt = $conn->prepare("SELECT id FROM week_plan WHERE week_number = ? AND year = ?");
    $stmt->execute([$week_number, $year]);
    $existing_plan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing_plan) {
        // Wenn der Wochenplan noch nicht existiert, erstelle einen neuen Eintrag
        $stmt = $conn->prepare("INSERT INTO week_plan (week_number, year) VALUES (?, ?)");
        $stmt->execute([$week_number, $year]);
        echo "Wochenplan für Woche $week_number im Jahr $year wurde erfolgreich erstellt!";
    } else {
        echo "Wochenplan für diese Woche existiert bereits!";
    }
}

// Vorhandene Wochenpläne anzeigen
$weekPlans = $conn->query("SELECT * FROM week_plan ORDER BY year, week_number")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Wochenplan für Essen</h1>

<!-- Link zur Rezeptverwaltung hinzufügen -->
<h2>Rezeptverwaltung</h2>
<ul>
    <li><a href="src/view_recipes.php">Rezepte anzeigen</a></li>
    <li><a href="src/add_recipe.php">Neues Rezept hinzufügen</a></li>
</ul>

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
