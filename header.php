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
    <link rel="stylesheet" href="/essensplan/assets/style.css"> <!-- Pfad zur style.css überprüfen -->
</head>
<body onload="checkDarkMode()"> <!-- Überprüft den Dark Mode beim Laden -->
<header>
    <div class="header-container">
        <h1><?php echo $title; ?></h1>
        <div class="menu-toggle" onclick="toggleMenu()">&#9776;</div>
    </div>
    <nav id="menu" class="hidden"> <!-- Versteckt das Menü initial -->
        <ul>
            <li><a href="/essensplan/index.php">Home</a></li>
            <li><a href="/essensplan/src/view_recipes.php">Rezepte verwalten</a></li>
            <li><a href="/essensplan/src/view_categories.php">Mahlzeitenkategorien verwalten</a></li>
            <li><a href="/essensplan/src/view_weeks.php">Wochenpläne verwalten</a></li> <!-- Wochenpläne verwalten -->
            <li><a href="/essensplan/src/archived_weeks.php">Archivierte Essenspläne</a></li> <!-- Archivierte Essenspläne -->
            <li><a href="javascript:void(0);" onclick="toggleDarkMode()">Dark Mode umschalten</a></li> <!-- Dark Mode Umschaltung -->
        </ul>
    </nav>
</header>

<script>
    // Funktion zum Umschalten des Burger-Menüs
    function toggleMenu() {
        var menu = document.getElementById("menu");
        menu.classList.toggle("active"); // Menü bei Klick anzeigen/verstecken
    }

    // Überprüfen, ob der Dark Mode aktiviert ist
    function checkDarkMode() {
        var darkMode = getCookie("darkMode");
        if (darkMode === "enabled") {
            document.body.classList.add("dark-mode");
        }
    }

    // Cookie-Wert abrufen
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
</script>
