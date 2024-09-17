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

// Filteroptionen anwenden
$whereClauses = [];
$params = [];

if (isset($_GET['year_filter']) && !empty($_GET['year_filter'])) {
    $whereClauses[] = "year = ?";
    $params[] = $_GET['year_filter'];
}

if (isset($_GET['week_filter']) && !empty($_GET['week_filter'])) {
    $whereClauses[] = "week_number = ?";
    $params[] = $_GET['week_filter'];
}

$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = "WHERE " . implode(" AND ", $whereClauses);
}

$query = "SELECT * FROM week_plan $whereSql ORDER BY year, week_number";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$weekPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Wochenplan für Essen</h1>

<!-- Filterformular für Wochenpläne -->
<form method="GET">
    <label for="year_filter">Jahr filtern:</label>
    <input type="number" name="year_filter" value="<?php echo isset($_GET['year_filter']) ? $_GET['year_filter'] : date('Y'); ?>">

    <label for="week_filter">Kalenderwoche filtern:</label>
    <select name="week_filter">
        <option value="">Alle Wochen</option>
        <?php for ($i = 1; $i <= 52; $i++): ?>
            <option value="<?php echo $i; ?>" <?php if (isset($_GET['week_filter']) && $_GET['week_filter'] == $i) echo 'selected'; ?>>
                Woche <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select>

    <input type="submit" value="Filtern">
</form>

<!-- Wochenplan erstellen -->
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
    <?php if (empty($weekPlans)): ?>
        <li>Keine Wochenpläne gefunden.</li>
    <?php else: ?>
        <?php foreach ($weekPlans as $plan): ?>
            <li>
                Woche <?php echo $plan['week_number']; ?> (Jahr <?php echo $plan['year']; ?>) - 
                <a href="src/view_week.php?week_plan_id=<?php echo $plan['id']; ?>">Ansehen</a> | 
                <a href="src/edit_week.php?week_plan_id=<?php echo $plan['id']; ?>">Bearbeiten</a> | 
                <a href="src/delete_week.php?week_plan_id=<?php echo $plan['id']; ?>">Löschen</a>
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
