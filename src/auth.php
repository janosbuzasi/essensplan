// auth.php
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /essensplan/src/login.php");
    exit;
}
?>
