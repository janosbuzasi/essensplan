<?php
$title = "Neuen Wochenplan hinzufügen";
require '../header.php';
require_once '../config/db.php';

// Datenbankverbindung herstellen
$db = new Database();
$conn = $db->getConnection();

// Standardjahr festlegen (aktuelles Jahr)
$currentYear = date('Y');

// Überprüfen, ob ein Jahr ausgewählt wurde, sonst Standardjahr verwenden
$selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;

// Alle existierenden Wochenpläne des ausgewählten Jahres abrufen
$stmt = $conn->prepare("SELECT week_number FROM essensplan WHERE year = ?");
$stmt->execute([$selectedYear]);
$existingWeeks = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Alle möglichen Kalenderwochen (1-52)
$allWeeks = range(1, 52);

// Verfügbare Wochen berechnen
$availableWeeks = array_diff($allWeeks, $existingWeeks);
?>
<main>
    <h2><?php echo $title; ?></h2>

    <!-- Jahr auswählen und Formular automatisch absenden -->
    <form action="add_week.php" method="get">
        <label for="year">Jahr auswählen:</label>
        <select name="year" onchange="this.form.submit()">
            <?php
            // Zeige die letzten 5 Jahre und die nächsten 5 Jahre im Dropdown an
            for ($year = $currentYear - 5; $year <= $currentYear + 5; $year++) {
                $selected = ($year == $selectedYear) ? "selected" : "";
                echo "<option value='$year' $selected>$year</option>";
            }
            ?>
        </select>
    </form>

    <?php if (!empty($availableWeeks)): ?>
        <!-- Formular zur Auswahl einer Kalenderwoche -->
        <form action="add_week.php" method="post">
            <input type="hidden" name="year" value="<?php echo $selectedYear; ?>">
            
            <label for="week_number">Verfügbare Kalenderwochen für das Jahr <?php echo $selectedYear; ?>:</label>
            <select name="week_number" required>
                <?php foreach ($availableWeeks as $week): ?>
                    <option value="<?php echo $week; ?>">Woche <?php echo $week; ?></option>
                <?php endforeach; ?>
            </select><br>
            
            <input type="submit" value="Wochenplan hinzufügen" class="btn btn-add">
        </form>
    <?php else: ?>
        <p>Alle Kalenderwochen für das Jahr <?php echo $selectedYear; ?> sind bereits definiert.</p>
    <?php endif; ?>

    <?php
    // Verarbeitung des Formulars zum Hinzufügen des Wochenplans
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $year = $_POST['year'];
        $weekNumber = $_POST['week_number'];

        // Überprüfen, ob der Wochenplan bereits existiert
        $stmt = $conn->prepare("SELECT 1 FROM essensplan WHERE week_number = ? AND year = ?");
        $stmt->execute([$weekNumber, $year]);
        $existingWeekPlan = $stmt->fetchColumn();

        if ($existingWeekPlan) {
            echo "<p>Ein Wochenplan für Woche $weekNumber im Jahr $year existiert bereits.</p>";
        } else {
            // Wochenplan hinzufügen
            $stmt = $conn->prepare("INSERT INTO essensplan (week_number, year) VALUES (?, ?)");
            if ($stmt->execute([$weekNumber, $year])) {
                echo "<p>Wochenplan erfolgreich hinzugefügt!</p>";
            } else {
                echo "<p>Fehler beim Hinzufügen des Wochenplans.</p>";
            }
        }
    }
    ?>

    <!-- Zurück zur Übersicht -->
    <a href="view_weeks.php" class="btn btn-view">Zurück zur Übersicht</a>
</main>
<?php
include '../footer.php';
?>
