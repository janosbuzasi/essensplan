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
    $targetUserId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    $flash = ['type' => 'error', 'message' => 'Das Passwort konnte nicht geändert werden.'];

    if (!hash_equals($_SESSION['user_management_csrf'], $csrf)) {
        $flash['message'] = 'Ungültige oder abgelaufene Anfrage. Bitte erneut versuchen.';
    } elseif (!password_verify($currentPassword, $currentAdmin['password'])) {
        $flash['message'] = 'Dein aktuelles Admin-Passwort ist falsch.';
    } elseif (!$targetUserId) {
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
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
            $updateStmt->execute([$passwordHash, $targetUserId]);
            $flash = [
                'type' => 'success',
                'message' => 'Das Passwort für ' . $targetUsername . ' wurde geändert.',
            ];
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
    <p>Hier kannst du als Administrator das Passwort eines vorhandenen Benutzers neu setzen.</p>

    <?php if (is_array($flash)): ?>
        <p class="alert alert-<?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php endif; ?>

    <form method="post" class="recipe-form user-management-form">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['user_management_csrf'], ENT_QUOTES, 'UTF-8'); ?>">

        <div class="form-group">
            <label for="user_id">Benutzer</label>
            <select id="user_id" name="user_id" required>
                <?php foreach ($users as $user): ?>
                    <option value="<?php echo (int) $user['id']; ?>">
                        <?php echo htmlspecialchars($user['username'] . ' (' . $user['role'] . ')', ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="current_password">Dein aktuelles Admin-Passwort</label>
            <input type="password" id="current_password" name="current_password" autocomplete="current-password" required>
        </div>

        <div class="form-group">
            <label for="new_password">Neues Passwort</label>
            <input type="password" id="new_password" name="new_password" minlength="12" autocomplete="new-password" required>
            <small class="form-help">Mindestens 12 Zeichen. Verwende kein Standardpasswort.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Neues Passwort bestätigen</label>
            <input type="password" id="confirm_password" name="confirm_password" minlength="12" autocomplete="new-password" required>
        </div>

        <button type="submit" class="btn btn-edit"><i class="fas fa-key"></i> Passwort ändern</button>
    </form>

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
