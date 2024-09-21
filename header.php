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
    <!-- Dynamisch das Standard-Stylesheet einbinden -->
    <?php
    echo '<link rel="stylesheet" href="styles/style.css">';
    ?>
</head>
<body>

<!-- Header-Bereich mit Burger-Menü -->
<header>
    <h1>
        <?php
        // Wenn $title existiert, diesen verwenden, sonst die Domain anzeigen
        echo isset($title) ? "$title | $domain" : $domain;
        ?>
    </h1>
    <div class="menu-toggle" onclick="toggleMenu()">&#9776;</div> <!-- Burger-Icon -->
    <nav id="menu"> <!-- ID sollte "menu" sein, damit JavaScript es steuert -->
        <ul>
            <li><a href="index.php">Home</a></li>
            <li>
                <a href="#">Rezepte</a>
                <ul>
                    <li><a href="src/view_recipes.php">Alle Rezepte anzeigen</a></li>
                    <li><a href="src/add_recipe.php">Neues Rezept hinzufügen</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Kategorien</a>
                <ul>
                    <li><a href="src/view_categories.php">Alle Kategorien anzeigen</a></li>
                    <li><a href="src/add_category.php">Neue Kategorie hinzufügen</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Wochenpläne</a>
                <ul>
                    <li><a href="src/index.php">Alle Wochenpläne anzeigen</a></li>
                    <li><a href="src/add_week.php">Neuen Wochenplan hinzufügen</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<script>
    function toggleMenu() {
        var menu = document.getElementById("menu");
        if (menu.classList.contains('active')) {
            menu.classList.remove('active');
        } else {
            menu.classList.add('active');
        }
    }
</script>
