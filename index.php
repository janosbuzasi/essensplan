<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wochenplan für Essen</title>
    <link rel="stylesheet" href="assets/style.css"> <!-- Verlinkung zur CSS-Datei -->
</head>
<body>
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
        <?php echo "Wochenplan ID: " . $plan['id'] . " - Woche: " . $plan['week_number'] . " - Jahr: " . $plan['year']; ?>
        <li>
            Woche <?php echo $plan['week_number']; ?> (Jahr <?php echo $plan['year']; ?>) - 
            <a href="src/view_week.php?week_plan_id=<?php echo $plan['id']; ?>">Ansehen</a> | 
            <a href="src/edit_week.php?week_plan_id=<?php echo $plan['id']; ?>">Bearbeiten</a> | 
            <a href="src/delete_week.php?week_plan_id=<?php echo $plan['id']; ?>" onclick="return confirm('Möchtest du diesen Wochenplan wirklich löschen?');">Löschen</a>
        </li>
    <?php endforeach; ?>
</ul>
</body>
</html>
