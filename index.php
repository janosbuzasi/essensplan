<?php
session_start(); // Start der Session zur Verwaltung der Benutzeranmeldung
$title = "Willkommen auf dem Essensplan-Manager";
require_once 'header.php'; // Inkludiere den Header

// Überprüfen, ob der Benutzer eingeloggt ist
$userLoggedIn = isset($_SESSION['username']);
?>
<main>
    <h2><?php echo $title; ?></h2>
    <?php if ($userLoggedIn): ?>
        <p>Hallo <?php echo $_SESSION['username']; ?>, willkommen zurück!</p>
        <p>Hier kannst du deine Essenspläne und Rezepte verwalten:</p>
        <ul>
            <li><a href="/essensplan/src/view_recipes.php" class="btn btn-view"><i class="fas fa-utensils"></i> Rezepte verwalten</a></li>
            <li><a href="/essensplan/src/view_weeks.php" class="btn btn-view"><i class="fas fa-calendar-alt"></i> Wochenpläne verwalten</a></li>
            <li><a href="/essensplan/src/archived_weeks.php" class="btn btn-view"><i class="fas fa-archive"></i> Archivierte Essenspläne</a></li>
        </ul>
    <?php else: ?>
        <p>Willkommen auf dem Essensplan-Manager!</p>
        <p>Bitte <a href="/essensplan/src/login.php" class="btn btn-add"><i class="fas fa-sign-in-alt"></i> Melde dich an</a>, um fortzufahren.</p>
    <?php endif; ?>
</main>
<?php
include 'footer.php';
?>
