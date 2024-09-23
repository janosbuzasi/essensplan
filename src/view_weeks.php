<?php
$title = "Wochenpläne verwalten";
require '../header.php';
require_once '../config/db.php';

$db = new Database();
$conn = $db->getConnection();

// Debugging: Überprüfen, ob die Verbindung erfolgreich ist
if (!$conn) {
    die("Datenbankverbindung fehlgeschlagen!");
}

// Vorhandene Wochenpläne abrufen
$stmt = $conn->query("SELECT * FROM essensplan ORDER BY year DESC, week_number DESC");
$weekPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Anzahl der abgerufenen Wochenpläne anzeigen
echo "<pre>";
echo "Anzahl der vorhandenen Wochenpläne: " . count($weekPlans) . "\n";
print_r($weekPlans);
echo "</pre>";

function hasAssignments($conn, $week_plan_id) {
    // Überprüfen, ob Zuweisungen für den Wochenplan existieren
    $stmt = $conn->prepare("SELECT COUNT(*) FROM meal_plan WHERE week_plan_id = ?");
    $stmt->execute([$week_plan_id]);
    return $stmt->fetchColumn() > 0;
}
?>

<main>
    <h2><?php echo $title; ?></h2>
    
    <?php if ($weekPlans): ?>
        <table class="styled-table"> <!-- Nutzung der bestehenden CSS-Klassen -->
            <thead>
                <tr>
                    <th>Woche</th>
                    <th>Jahr</th>
                    <th>Beschreibung</th>
                    <th>Status</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($weekPlans as $plan): ?>
                    <tr>
                        <td>Woche <?php echo $plan['week_number']; ?></td>
                        <td><?php echo $plan['year']; ?></td>
                        <td><?php echo htmlspecialchars($plan['description'], ENT_QUOTES); ?></td>
                        <td><?php echo $plan['status']; ?></td>
                        <td>
                            <a href="view_week.php?id=<?php echo $plan['id']; ?>" class="btn btn-view">Ansehen</a>
                            <a href="edit_week.php?id=<?php echo $plan['id']; ?>" class="btn btn-edit">Bearbeiten</a>
                            <a href="delete_week.php?id=<?php echo $plan['id']; ?>" class="btn btn-delete" onclick="return confirm('Möchtest du diesen Wochenplan wirklich löschen?');">Löschen</a>
                            <?php if (hasAssignments($conn, $plan['id'])): ?>
                                <a href="edit_assignment.php?week_plan_id=<?php echo $plan['id']; ?>" class="btn btn-edit">Zuweisungen bearbeiten</a>
                            <?php else: ?>
                                <a href="assign_recipe_to_week.php?week_plan_id=<?php echo $plan['id']; ?>" class="btn btn-add">Rezepte zuweisen</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Keine Wochenpläne gefunden.</p>
    <?php endif; ?>
</main>

<?php
include '../footer.php';
?>
