<?php
// (Vollständiges Skript wie zuvor)

    // Bestehende Zuordnungen anzeigen
    echo "<h3>Bestehende Zuordnungen</h3>";
    $stmt = $conn->query("
        SELECT er.id, wp.week_number, wp.year, er.day_of_week, mc.name AS meal_category, r.title AS recipe_title
        FROM essensplan_recipes er
        JOIN essensplan wp ON er.essensplan_id = wp.id
        JOIN recipes r ON er.recipe_id = r.id
        JOIN meal_categories mc ON er.meal_category_id = mc.id
        ORDER BY wp.year, wp.week_number, FIELD(er.day_of_week, 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
                 FIELD(mc.name, 'Frühstück', 'Znüni', 'Mittagessen', 'Zvieri', 'Abendessen')
    ");
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($assignments) {
        echo "<table border='1'>";
        echo "<tr><th>Woche</th><th>Tag</th><th>Kategorie</th><th>Rezept</th><th>Aktion</th></tr>";
        foreach ($assignments as $assignment) {
            echo "<tr>
                    <td>Woche " . $assignment['week_number'] . " im Jahr " . $assignment['year'] . "</td>
                    <td>" . $assignment['day_of_week'] . "</td>
                    <td>" . $assignment['meal_category'] . "</td>
                    <td>" . $assignment['recipe_title'] . "</td>
                    <td><a href='edit_assignment.php?id=" . $assignment['id'] . "'>Bearbeiten</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Keine Zuordnungen gefunden.</p>";
    }
?>
