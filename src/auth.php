<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '/essensplan/index.php');
    $returnUrl = '/essensplan/index.php';

    if (preg_match('#^/essensplan/[A-Za-z0-9._~!$&\'()*+,;=:@/%?-]*$#', $requestUri) === 1) {
        $returnUrl = $requestUri;
    }

    header('Location: /essensplan/src/login.php?return_url=' . urlencode($returnUrl));
    exit;
}
