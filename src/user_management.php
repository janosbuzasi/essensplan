<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

if (!isset($_SESSION['user_id'])) {
    header('Location: /essensplan/src/login.php');
    exit;
}

$adminStmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE id = ? LIMIT 1');
$adminStmt->execute([(int) $_SESSION['user_id']]);
$currentAdmin = $adminStmt->fetch(PDO::FETCH_ASSOC);

if (!$currentAdmin || $currentAdmin['role'] !== 'admin') {
    http_response_code(403);
    exit('Zugriff verweigert.');
}

if (!isset($_SESSION['user_management_csrf'])) {
    $_SESSION['user_management_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = (string) ($_POST['csrf_token'] ?? '');
    $action = (string) ($_POST['action'] ?? '');
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $flash = ['type' => 'error', 'message' => 'Die Aktion konnte nicht ausgeführt werden.'];

    if (!hash_equals($_SESSION['user_management_csrf'], $csrf)) {
        $flash['message'] = 'Ungültige oder abgelaufene Anfrage. Bitte erneut versuchen.';
    } elseif (!password_verify($currentPassword, $currentAdmin['password'])) {
        $flash['message'] = 'Dein aktuelles Admin-Passwort ist falsch.';
    } elseif ($action === 'reset_password') {
        $targetUserId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if (!$targetUserId) {
            $flash['message'] = 'Bitte einen gültigen Benutzer auswählen.';
        } elseif (strlen($newPassword) < 12) {
            $flash['message'] = 'Das neue Passwort muss mindestens 12 Zeichen lang sein.';
        } elseif ($newPassword !== $confirmPassword) {
            $flash['message'] = 'Die beiden neuen Passwörter stimmen nicht überein.';
        } else {
            $targetStmt = $conn->prepare('SELECT username FROM users WHERE id = ? LIMIT 1');
            $targetStmt->execute([$targetUserId]);
            $targetUsername = $targetStmt->fetchColumn();

            if ($targetUsername === false) {
                $flash['message'] = 'Der ausgewählte Benutzer wurde nicht gefunden.';
            } else {
                $updateStmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
                $updateStmt->execute([password_hash($newPassword, PASSWORD_DEFAULT), $targetUserId]);
                $flash = [
                    'type' => 'success',
                    'message' => 'Das Passwort für ' . $targetUsername . ' wurde geändert.',
                ];
            }
        }
    } elseif ($action === 'create_user') {
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $newPassword = (string) ($_POST['user_password'] ?? '');
        $confirmPassword = (string) ($_POST['user_confirm_password'] ?? '');

        if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
            $flash['message'] = 'Der Benutzername muss 3 bis 50 Zeichen lang sein und darf Buchstaben, Zahlen, Punkt, Unterstrich und Bindestrich enthalten.';
        } elseif (strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $flash['message'] = 'Bitte eine gültige E-Mail-Adresse eingeben.';
        } elseif (strlen($newPassword) < 12) {
            $flash['message'] = 'Das Passwort muss mindestens 12 Zeichen lang sein.';
        } elseif ($newPassword !== $confirmPassword) {
            $flash['message'] = 'Die beiden Passwörter stimmen nicht überein.';
        } else {
            $duplicateStmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
            $duplicateStmt->execute([$username, $email]);

            if ($duplicateStmt->fetchColumn() !== false) {
                $flash['message'] = 'Benutzername oder E-Mail-Adresse wird bereits verwendet.';
            } else {
                $insertStmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
                $insertStmt->execute([$username, password_hash($newPassword, PASSWORD_DEFAULT), $email]);
                $flash = [
                    'type' => 'success',
                    'message' => 'Der Benutzer ' . $username . ' wurde ohne Adminrechte angelegt.',
                ];
            }
        }
    } elseif ($action === 'update_email') {
        $targetUserId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        $email = trim((string) ($_POST['email'] ?? ''));

        if (!$targetUserId) {
            $flash['message'] = 'Bitte einen gültigen Benutzer auswählen.';
        } elseif (strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $flash['message'] = 'Bitte eine gültige E-Mail-Adresse eingeben.';
        } else {
            $targetStmt = $conn->prepare('SELECT username FROM users WHERE id = ? LIMIT 1');
            $targetStmt->execute([$targetUserId]);
            $targetUsername = $targetStmt->fetchColumn();

            $duplicateStmt = $conn->prepare('SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
            $duplicateStmt->execute([$email, $targetUserId]);

            if ($targetUsername === false) {
                $flash['message'] = 'Der ausgewählte Benutzer wurde nicht gefunden.';
            } elseif ($duplicateStmt->fetchColumn() !== false) {
                $flash['message'] = 'Diese E-Mail-Adresse wird bereits von einem anderen Konto verwendet.';
            } else {
                $updateStmt = $conn->prepare('UPDATE users SET email = ? WHERE id = ?');
                $updateStmt->execute([$email, $targetUserId]);
                $flash = [
                    'type' => 'success',
                    'message' => 'Die E-Mail-Adresse für ' . $targetUsername . ' wurde geändert.',
                ];
            }
        }
    }

    $_SESSION['user_management_flash'] = $flash;
    $_SESSION['user_management_csrf'] = bin2hex(random_bytes(32));
    header('Location: /essensplan/src/user_management.php');
    exit;
}

$users = $conn->query('SELECT id, username, email, role, created_at FROM users ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);
$flash = $_SESSION['user_management_flash'] ?? null;
unset($_SESSION['user_management_flash']);

$title = 'Benutzerverwaltung';
require '../header.php';
?>
<main>
    <h2><i class="fas fa-users-cog"></i> Benutzerverwaltung</h2>
    <p>Administratoren können Benutzer ohne Adminrechte anlegen und Passwörter aller vorhandenen Konten zurücksetzen.</p>

    <?php if (is_array($flash)): ?>
        <p class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php endif; ?>

    <div class="user-management-grid">
        <section>
            <h3>Neuen Benutzer anlegen</h3>
            <form method="post" class="recipe-form user-management-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['user_management_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="create_user">

                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input type="text" id="username" name="username" minlength="3" maxlength="50" pattern="[A-Za-z0-9._-]+" autocomplete="off" required>
                </div>

                <div class="form-group">
                    <label for="email">E-Mail-Adresse</label>
                    <input type="email" id="email" name="email" maxlength="100" autocomplete="off" required>
                </div>

                <div class="form-group">
                    <label for="user_password">Passwort</label>
                    <input type="password" id="user_password" name="user_password" minlength="12" autocomplete="new-password" required>
                </div>

                <div class="form-group">
                    <label for="user_confirm_password">Passwort bestätigen</label>
                    <input type="password" id="user_confirm_password" name="user_confirm_password" minlength="12" autocomplete="new-password" required>
                </div>

                <div class="form-group">
                    <label for="create_current_password">Dein aktuelles Admin-Passwort</label>
                    <input type="password" id="create_current_password" name="current_password" autocomplete="current-password" required>
                </div>

                <p class="form-help">Das neue Konto erhält automatisch die Rolle <strong>user</strong>.</p>
                <button type="submit" class="btn btn-add"><i class="fas fa-user-plus"></i> Benutzer anlegen</button>
            </form>
        </section>

        <section>
            <h3>Passwort zurücksetzen</h3>
            <form method="post" class="recipe-form user-management-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['user_management_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="reset_password">

                <div class="form-group">
                    <label for="user_id">Benutzer oder Administrator</label>
                    <select id="user_id" name="user_id" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo (int) $user['id']; ?>">
                                <?php echo htmlspecialchars($user['username'] . ' (' . $user['role'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reset_current_password">Dein aktuelles Admin-Passwort</label>
                    <input type="password" id="reset_current_password" name="current_password" autocomplete="current-password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">Neues Passwort</label>
                    <input type="password" id="new_password" name="new_password" minlength="12" autocomplete="new-password" required>
                    <small class="form-help">Mindestens 12 Zeichen. Das bisherige Passwort des Zielkontos wird nicht benötigt.</small>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Neues Passwort bestätigen</label>
                    <input type="password" id="confirm_password" name="confirm_password" minlength="12" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn btn-edit"><i class="fas fa-key"></i> Passwort ändern</button>
            </form>
        </section>

        <section>
            <h3>E-Mail-Adresse ändern</h3>
            <form method="post" class="recipe-form user-management-form">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['user_management_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="update_email">

                <div class="form-group">
                    <label for="email_user_id">Benutzer oder Administrator</label>
                    <select id="email_user_id" name="user_id" required>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo (int) $user['id']; ?>">
                                <?php echo htmlspecialchars($user['username'] . ' (' . $user['email'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="updated_email">Neue E-Mail-Adresse</label>
                    <input type="email" id="updated_email" name="email" maxlength="100" autocomplete="off" required>
                </div>

                <div class="form-group">
                    <label for="email_current_password">Dein aktuelles Admin-Passwort</label>
                    <input type="password" id="email_current_password" name="current_password" autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn btn-edit"><i class="fas fa-envelope"></i> E-Mail-Adresse ändern</button>
            </form>
        </section>
    </div>

    <div class="user-table-wrap">
        <table class="styled-table">
            <thead>
                <tr><th>Benutzer</th><th>E-Mail</th><th>Rolle</th><th>Erstellt</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($user['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../footer.php'; ?>
