<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';
$db = new Database();
$conn = $db->getConnection();
$error = '';
$returnUrl = (string) ($_GET['return_url'] ?? $_POST['return_url'] ?? '/essensplan/index.php');

if (preg_match('#^/essensplan/[A-Za-z0-9._~!$&\'()*+,;=:@/%?-]*$#', $returnUrl) !== 1) {
    $returnUrl = '/essensplan/index.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username !== '' && $password !== '') {
        $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            header('Location: ' . $returnUrl);
            exit;
        }
    }

    $error = 'Benutzername oder Passwort ist falsch.';
}

$title = 'Anmeldung';
require '../header.php';
?>
<main>
    <h2><i class="fas fa-sign-in-alt"></i> <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php if ($error !== ''): ?>
        <p class="alert alert-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <form method="post" class="login-form">
        <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="username"><i class="fas fa-user"></i> Benutzername:</label>
            <input type="text" name="username" id="username" autocomplete="username" required autofocus>
        </div>
        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Passwort:</label>
            <input type="password" name="password" id="password" autocomplete="current-password" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-edit"><i class="fas fa-sign-in-alt"></i> Anmelden</button>
            <button type="reset" class="btn btn-delete"><i class="fas fa-undo"></i> Zurücksetzen</button>
        </div>
    </form>
    <a href="../index.php" class="btn btn-view"><i class="fas fa-arrow-left"></i> Zurück</a>
</main>

<?php include '../footer.php'; ?>
