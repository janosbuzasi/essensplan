<?php
session_start(); // Start der Session zur Verwaltung der Benutzeranmeldung
require_once '../config/db.php'; // Datenbankverbindung
$db = new Database();
$conn = $db->getConnection();

// Benutzerinformationen abrufen, wenn eingeloggt
$userLoggedIn = isset($_SESSION['username']);
?>

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
    <!-- Font Awesome einbinden -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Standard-Styling -->
    <link rel="stylesheet" href="/essensplan/assets/style.css"> 

</head>
<body>
<header>
    <div class="header-container">
        <h1><?php echo $title; ?></h1>
        <div class="menu-toggle" onclick="toggleMenu()">&#9776;</div>
    </div>
    <nav id="menu" class="hidden"> <!-- Versteckt das Menü initial -->
        <ul>
            <li><a href="/essensplan/index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="/essensplan/src/view_recipes.php"><i class="fas fa-utensils"></i> Rezepte verwalten</a></li>
            <li><a href="/essensplan/src/view_categories.php"><i class="fas fa-list"></i> Mahlzeitenkategorien verwalten</a></li>
            <li><a href="/essensplan/src/view_weeks.php"><i class="fas fa-calendar-alt"></i> Wochenpläne verwalten</a></li>
            <li><a href="/essensplan/src/archived_weeks.php"><i class="fas fa-archive"></i> Archivierte Essenspläne</a></li>
            <?php if ($userLoggedIn): ?>
                <li><a href="/essensplan/src/logout.php"><i class="fas fa-sign-out-alt"></i> Abmelden (<?php echo $_SESSION['username']; ?>)</a></li>
            <?php else: ?>
                <li><a href="/essensplan/src/login.php"><i class="fas fa-sign-in-alt"></i> Anmelden</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<script>
    // Funktion zum Umschalten des Burger-Menüs
    function toggleMenu() {
        var menu = document.getElementById("menu");
        menu.classList.toggle("active"); // Menü bei Klick anzeigen/verstecken
    }
</script>
