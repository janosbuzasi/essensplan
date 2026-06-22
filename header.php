<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/db.php';
$db = new Database();
$conn = $db->getConnection();
$userLoggedIn = isset($_SESSION['username']);
$domain = $_SERVER['HTTP_HOST'] ?? 'webtrash.ch';
$pageTitle = isset($title) ? "$title | $domain" : $domain;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/essensplan/assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-container">
        <a class="site-brand" href="/">
            <span class="brand-mark" aria-hidden="true">WT</span>
            <span>webtrash.ch</span>
        </a>
        <span class="app-name">Essensplan</span>
        <button class="menu-toggle" type="button" aria-controls="menu" aria-expanded="false">
            <span class="menu-icon" aria-hidden="true"></span>
            <span class="sr-only">Menü öffnen</span>
        </button>
    </div>
    <nav id="menu" aria-label="Essensplan-Navigation">
        <ul>
            <li><a href="/essensplan/index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="/essensplan/src/view_recipes.php"><i class="fas fa-utensils"></i> Rezepte</a></li>
            <li><a href="/essensplan/src/view_categories.php"><i class="fas fa-list"></i> Kategorien</a></li>
            <li><a href="/essensplan/src/view_weeks.php"><i class="fas fa-calendar-alt"></i> Wochenpläne</a></li>
            <li><a href="/essensplan/src/archived_weeks.php"><i class="fas fa-archive"></i> Archiv</a></li>
            <?php if ($userLoggedIn): ?>
                <li class="menu-separator"><a href="/essensplan/src/logout.php"><i class="fas fa-sign-out-alt"></i> Abmelden (<?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>)</a></li>
            <?php else: ?>
                <li class="menu-separator"><a href="/essensplan/src/login.php"><i class="fas fa-sign-in-alt"></i> Anmelden</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.querySelector('.menu-toggle');
    const menu = document.getElementById('menu');

    if (!button || !menu) return;

    button.addEventListener('click', function () {
        const open = menu.classList.toggle('active');
        button.setAttribute('aria-expanded', String(open));
        button.querySelector('.sr-only').textContent = open ? 'Menü schließen' : 'Menü öffnen';
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            menu.classList.remove('active');
            button.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>
