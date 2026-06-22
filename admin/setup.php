<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

function setup_table_exists(PDO $conn, string $table): bool
{
    $stmt = $conn->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?');
    $stmt->execute([$table]);
    return (int) $stmt->fetchColumn() > 0;
}

function setup_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$databaseExists = setup_table_exists($conn, 'users');
$setupAdmin = null;

if ($databaseExists) {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        exit('Bitte zuerst als Administrator anmelden.');
    }

    $adminStmt = $conn->prepare('SELECT password, role FROM users WHERE id = ? LIMIT 1');
    $adminStmt->execute([(int) $_SESSION['user_id']]);
    $setupAdmin = $adminStmt->fetch(PDO::FETCH_ASSOC);

    if (!$setupAdmin || $setupAdmin['role'] !== 'admin') {
        http_response_code(403);
        exit('Zugriff verweigert.');
    }
}

if (!isset($_SESSION['setup_csrf'])) {
    $_SESSION['setup_csrf'] = bin2hex(random_bytes(32));
}

$error = '';
$setupComplete = false;
$initialAdminPassword = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = (string) ($_POST['csrf_token'] ?? '');

    if (!hash_equals($_SESSION['setup_csrf'], $csrf)) {
        $error = 'Ungültige oder abgelaufene Anfrage.';
    } elseif ($databaseExists && !password_verify((string) ($_POST['password'] ?? ''), $setupAdmin['password'])) {
        $error = 'Das aktuelle Admin-Passwort ist falsch.';
    } else {
        if ($databaseExists) {
            $conn->exec('DROP TABLE IF EXISTS essensplan_recipes, recipes, meal_categories, essensplan, users');
        }

        $conn->exec("
            CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                role ENUM('admin', 'user') DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ");

        $conn->exec("
            CREATE TABLE essensplan (
                id INT AUTO_INCREMENT PRIMARY KEY,
                week_number INT NOT NULL,
                year INT NOT NULL,
                description TEXT,
                status ENUM('aktiv', 'archiviert') DEFAULT 'aktiv',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ");

        $conn->exec("
            CREATE TABLE recipes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                ingredients TEXT,
                instructions TEXT,
                category VARCHAR(100),
                prep_time INT,
                cook_time INT,
                difficulty ENUM('leicht', 'mittel', 'schwer'),
                servings INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ");

        $conn->exec("
            CREATE TABLE meal_categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ");

        $conn->exec("
            CREATE TABLE essensplan_recipes (
                id INT AUTO_INCREMENT PRIMARY KEY,
                essensplan_id INT NOT NULL,
                recipe_id INT NOT NULL,
                day_of_week ENUM('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
                meal_category_id INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (essensplan_id) REFERENCES essensplan(id) ON DELETE CASCADE,
                FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
                FOREIGN KEY (meal_category_id) REFERENCES meal_categories(id) ON DELETE CASCADE
            ) ENGINE=InnoDB
        ");

        $initialAdminPassword = bin2hex(random_bytes(10));
        $adminInsert = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES ('admin', ?, 'admin@example.com', 'admin')");
        $adminInsert->execute([password_hash($initialAdminPassword, PASSWORD_DEFAULT)]);

        $categoryInsert = $conn->prepare('INSERT INTO meal_categories (name, description) VALUES (?, ?)');
        foreach ([
            ['Frühstück', 'Morgendliche Mahlzeit'],
            ['Znüni', 'Zwischenmahlzeit am Vormittag'],
            ['Mittagessen', 'Hauptmahlzeit am Mittag'],
            ['Zvieri', 'Zwischenmahlzeit am Nachmittag'],
            ['Abendessen', 'Abendliche Mahlzeit'],
        ] as $category) {
            $categoryInsert->execute($category);
        }

        $recipeInsert = $conn->prepare('INSERT INTO recipes (title, ingredients, instructions, category, prep_time, cook_time, difficulty, servings) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ([
            ['Pancakes', 'Mehl, Milch, Eier, Zucker, Salz, Backpulver', 'Alle Zutaten vermischen und in einer Pfanne ausbacken.', 'Frühstück', 10, 15, 'leicht', 4],
            ['Obstsalat', 'Äpfel, Bananen, Orangen, Honig', 'Alles in Stücke schneiden und vermischen.', 'Znüni', 5, 0, 'leicht', 2],
            ['Spaghetti Bolognese', 'Spaghetti, Hackfleisch, Tomaten, Zwiebeln, Knoblauch, Olivenöl', 'Hackfleisch anbraten und mit den übrigen Zutaten köcheln lassen.', 'Mittagessen', 10, 30, 'leicht', 4],
            ['Käsebrot', 'Brot, Butter, Käse, Gurkenscheiben', 'Brot belegen und servieren.', 'Zvieri', 5, 0, 'leicht', 1],
            ['Hähnchenbrust mit Gemüse', 'Hähnchenbrust, Brokkoli, Karotten, Olivenöl, Salz, Pfeffer', 'Hähnchenbrust braten und mit Gemüse servieren.', 'Abendessen', 15, 20, 'mittel', 2],
        ] as $recipe) {
            $recipeInsert->execute($recipe);
        }

        $weekInsert = $conn->prepare('INSERT INTO essensplan (week_number, year, description) VALUES (?, ?, ?)');
        $weekInsert->execute([(int) date('W'), (int) date('Y'), 'Beispielhafter Essensplan']);
        $weekPlanId = (int) $conn->lastInsertId();

        $categories = $conn->query('SELECT id FROM meal_categories ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
        $recipes = $conn->query('SELECT id FROM recipes ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
        $assignmentInsert = $conn->prepare('INSERT INTO essensplan_recipes (essensplan_id, recipe_id, day_of_week, meal_category_id) VALUES (?, ?, ?, ?)');
        $days = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];

        foreach ($days as $dayIndex => $day) {
            foreach ($categories as $categoryIndex => $categoryId) {
                $recipeId = $recipes[($dayIndex + $categoryIndex) % count($recipes)];
                $assignmentInsert->execute([$weekPlanId, $recipeId, $day, $categoryId]);
            }
        }

        $_SESSION = [];
        $_SESSION['setup_csrf'] = bin2hex(random_bytes(32));
        $setupComplete = true;
    }
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Essensplan Setup</title>
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <link rel="stylesheet" href="/essensplan/assets/style.css">
</head>
<body>
<main>
    <h1>Essensplan Setup</h1>

    <?php if ($error !== ''): ?>
        <p class="alert alert-error"><?php echo setup_escape($error); ?></p>
    <?php endif; ?>

    <?php if ($setupComplete): ?>
        <p class="alert alert-success">Das Setup wurde erfolgreich abgeschlossen.</p>
        <p><strong>Benutzername:</strong> admin</p>
        <p><strong>Einmaliges Startpasswort:</strong> <code><?php echo setup_escape($initialAdminPassword); ?></code></p>
        <p>Notiere das Passwort, melde dich an und ändere es sofort über die Benutzerverwaltung.</p>
        <a class="btn btn-add" href="/essensplan/src/login.php">Zur Anmeldung</a>
    <?php else: ?>
        <?php if ($databaseExists): ?>
            <p class="alert alert-error"><strong>Achtung:</strong> Das erneute Setup löscht sämtliche Essenspläne, Rezepte und Benutzer.</p>
        <?php else: ?>
            <p>Das Setup erstellt die Datenbanktabellen und einen Administrator mit einem zufälligen Startpasswort.</p>
        <?php endif; ?>

        <form method="post" class="recipe-form">
            <input type="hidden" name="csrf_token" value="<?php echo setup_escape($_SESSION['setup_csrf']); ?>">
            <?php if ($databaseExists): ?>
                <div class="form-group">
                    <label for="password">Aktuelles Admin-Passwort</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn <?php echo $databaseExists ? 'btn-delete' : 'btn-add'; ?>">
                <?php echo $databaseExists ? 'Datenbank zurücksetzen' : 'Setup starten'; ?>
            </button>
        </form>
    <?php endif; ?>
</main>
</body>
</html>
