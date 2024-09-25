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
        <div class="icon-menu">
            <a href="/essensplan/index.php" class="icon-menu-item" title="Home">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            <a href="/essensplan/src/view_recipes.php" class="icon-menu-item" title="Rezepte verwalten">
                <i class="fas fa-utensils"></i>
                <span>Rezepte verwalten</span>
            </a>
            <a href="/essensplan/src/view_categories.php" class="icon-menu-item" title="Mahlzeitenkategorien verwalten">
                <i class="fas fa-list"></i>
                <span>Mahlzeitenkategorien</span>
            </a>
            <a href="/essensplan/src/view_weeks.php" class="icon-menu-item" title="Wochenpläne verwalten">
                <i class="fas fa-calendar-alt"></i>
                <span>Wochenpläne</span>
            </a>
            <a href="/essensplan/src/archived_weeks.php" class="icon-menu-item" title="Archivierte Essenspläne">
                <i class="fas fa-archive"></i>
                <span>Archivierte Pläne</span>
            </a>
            <a href="/essensplan/src/logout.php" class="icon-menu-item" title="Abmelden">
                <i class="fas fa-sign-out-alt"></i>
                <span>Abmelden (<?php echo $_SESSION['username']; ?>)</span>
            </a>
        </div>
    <?php else: ?>
        <p>Willkommen auf dem Essensplan-Manager!</p>
        <p>Bitte <a href="/essensplan/src/login.php" class="btn btn-add"><i class="fas fa-sign-in-alt"></i> Melde dich an</a>, um fortzufahren.</p>
    <?php endif; ?>
</main>
<?php
include 'footer.php';
?>
