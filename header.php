<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        $domain = $_SERVER['HTTP_HOST'];
        $pageTitle = isset($title) ? "$title | $domain" : $domain;
        echo $pageTitle;
        ?>
    </title>
    <?php
    // Standardstil festlegen, falls kein Cookie gesetzt ist
    $current_style = isset($_COOKIE['style']) ? $_COOKIE['style'] : 'default';

    // Dynamisch das Stylesheet basierend auf dem Cookie einbinden
    if ($current_style === 'classic') {
        echo '<link rel="stylesheet" href="/essensplan/styles/classic_style.css">';
    } elseif ($current_style === 'yellow') {
        echo '<link rel="stylesheet" href="/essensplan/styles/yellow_style.css">';
    } else {
        echo '<link rel="stylesheet" href="/essensplan/styles/style.css">';
    }
    ?>
</head>
<body>

<!-- Header-Bereich mit Navigation -->
<header>
    <h1><?php echo isset($title) ? "$title | $domain" : $domain; ?></h1>
    <div class="menu-toggle" onclick="toggleMenu()">&#9776;</div> <!-- Burger-Icon für mobile Navigation -->
    <nav id="menu">
        <ul>
            <li><a href="/essensplan/index.php">Home</a></li>
            <li><a href="/essensplan/src/view_recipes.php">Rezeptverwaltung</a>
                <ul>
                    <li><a href="/essensplan/src/add_recipe.php">Neues Rezept hinzufügen</a></li>
                    <li><a href="/essensplan/src/view_recipes.php">Rezepte anzeigen</a></li>
                </ul>
            </li>
            <li><a href="/essensplan/src/view_categories.php">Mahlzeitenkategorienverwaltung</a>
                <ul>
                    <li><a href="/essensplan/src/add_category.php">Neue Kategorie hinzufügen</a></li>
                    <li><a href="/essensplan/src/view_categories.php">Kategorien anzeigen</a></li>
                </ul>
            </li>
            <li><a href="/essensplan/src/view_weeks.php">Wochenplanverwaltung</a>
                <ul>
                    <li><a href="/essensplan/src/add_week.php">Neue Woche hinzufügen</a></li>
                    <li><a href="/essensplan/src/view_weeks.php">Wochenpläne anzeigen</a></li>
                    <li><a href="/essensplan/src/archived_weeks.php">Archivierte Wochen anzeigen</a></li>
                </ul>
            </li>
            <li><a href="/essensplan/src/assign_recipe_to_week.php">Rezepte zu Wochenplänen</a></li>
        </ul>
    </nav>
</header>

<script>
    // Funktion zum Umschalten des Menüs auf mobilen Geräten
    function toggleMenu() {
        var menu = document.getElementById("menu");
        if (menu.classList.contains('active')) {
            menu.classList.remove('active');
        } else {
            menu.classList.add('active');
        }
    }
</script>
