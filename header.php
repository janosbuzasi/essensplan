<?php
session_start(); // Session starten
$css_file = isset($_SESSION['css_file']) ? $_SESSION['css_file'] : 'style.css'; // Dynamische CSS-Datei
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        $domain = $_SERVER['HTTP_HOST'];
        $pageTitle = isset($title) ? "$domain - $title" : $domain;
        echo $pageTitle;
        ?>
    </title>
    <link rel="stylesheet" href="assets/<?php echo $css_file; ?>"> <!-- Dynamische CSS-Datei -->
</head>
<body>
    <!-- Header-Bereich mit Navigation -->
    <header>
        <h1><?php echo $pageTitle; ?></h1>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="view_recipes.php">Rezepte anzeigen</a></li>
                <li><a href="add_recipe.php">Neues Rezept hinzufügen</a></li>
            </ul>
        </nav>
    </header>
