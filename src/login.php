<?php
$title = "Anmeldung";
require '../header.php'; // Header einfügen
require_once '../config/db.php';

session_start();
$db = new Database();
$conn = $db->getConnection();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: ../index.php');
            exit;
        } else {
            $error = "Benutzername oder Passwort ist falsch.";
        }
    } else {
        $error = "Bitte Benutzername und Passwort eingeben.";
    }
}
?>

<main>
    <h2><i class="fas fa-sign-in-alt"></i> <?php echo $title; ?></h2>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="post" class="login-form"> <!-- CSS-Klasse für das Formular -->
        <div class="form-group">
            <label for="username"><i class="fas fa-user"></i> Benutzername:</label>
            <input type="text" name="username" id="username" required>
        </div>
        
        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Passwort:</label>
            <input type="password" name="password" id="password" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-edit" title="Anmelden">
                <i class="fas fa-sign-in-alt"></i> Anmelden
            </button>
            <button type="reset" class="btn btn-delete" title="Zurücksetzen">
                <i class="fas fa-undo"></i> Zurücksetzen
            </button>
        </div>
    </form>
    
    <a href="../index.php" class="btn btn-view" title="Zurück zur Startseite">
        <i class="fas fa-arrow-left"></i> Zurück
    </a>
</main>

<?php
include '../footer.php'; // Footer einfügen
?>
