<?php
require_once 'auth.php';
require_once '../config/db.php';

function email_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect_with_email_flash(int $weekPlanId, string $type, string $message): void
{
    $_SESSION['week_email_flash'] = ['type' => $type, 'message' => $message];
    header('Location: /essensplan/src/view_week.php?id=' . $weekPlanId);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}

$weekPlanId = filter_input(INPUT_POST, 'week_plan_id', FILTER_VALIDATE_INT) ?: 0;
$csrf = (string) ($_POST['csrf_token'] ?? '');

if ($weekPlanId < 1) {
    redirect_with_email_flash(0, 'error', 'Ungültiger Wochenplan.');
}

if (!isset($_SESSION['week_email_csrf']) || !hash_equals($_SESSION['week_email_csrf'], $csrf)) {
    redirect_with_email_flash($weekPlanId, 'error', 'Ungültige oder abgelaufene Anfrage.');
}

$lastSentAt = (int) ($_SESSION['week_email_last_sent_at'] ?? 0);
if ($lastSentAt > time() - 60) {
    redirect_with_email_flash($weekPlanId, 'error', 'Bitte warte eine Minute, bevor du erneut eine E-Mail sendest.');
}

$db = new Database();
$conn = $db->getConnection();

$userStmt = $conn->prepare('SELECT username, email FROM users WHERE id = ? LIMIT 1');
$userStmt->execute([(int) $_SESSION['user_id']]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user || filter_var($user['email'], FILTER_VALIDATE_EMAIL) === false || preg_match('/[\r\n]/', $user['email'])) {
    redirect_with_email_flash($weekPlanId, 'error', 'Für dein Benutzerkonto ist keine gültige E-Mail-Adresse hinterlegt.');
}

$weekStmt = $conn->prepare('SELECT week_number, year, description FROM essensplan WHERE id = ? LIMIT 1');
$weekStmt->execute([$weekPlanId]);
$weekPlan = $weekStmt->fetch(PDO::FETCH_ASSOC);

if (!$weekPlan) {
    redirect_with_email_flash($weekPlanId, 'error', 'Der Wochenplan wurde nicht gefunden.');
}

$mealStmt = $conn->prepare("
    SELECT er.day_of_week, mc.name AS meal_category, r.title AS recipe_title,
           r.prep_time, r.cook_time, r.ingredients, r.instructions
    FROM essensplan_recipes er
    JOIN recipes r ON er.recipe_id = r.id
    JOIN meal_categories mc ON er.meal_category_id = mc.id
    WHERE er.essensplan_id = ?
    ORDER BY FIELD(er.day_of_week, 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'),
             FIELD(mc.name, 'Frühstück', 'Znüni', 'Mittagessen', 'Zvieri', 'Abendessen')
");
$mealStmt->execute([$weekPlanId]);
$meals = $mealStmt->fetchAll(PDO::FETCH_ASSOC);

if (!$meals) {
    redirect_with_email_flash($weekPlanId, 'error', 'Der Wochenplan enthält noch keine Mahlzeiten.');
}

$date = new DateTime();
$date->setISODate((int) $weekPlan['year'], (int) $weekPlan['week_number']);
$startDate = $date->format('d.m.Y');
$date->modify('+6 days');
$endDate = $date->format('d.m.Y');
$title = 'Essensplan KW ' . (int) $weekPlan['week_number'] . ' (' . $startDate . ' - ' . $endDate . ')';

$mealsByDay = [];
foreach ($meals as $meal) {
    $mealsByDay[$meal['day_of_week']][] = $meal;
}

$printUrl = 'https://webtrash.ch/essensplan/src/print.php?id=' . $weekPlanId;

$body = '<!doctype html><html lang="de"><head><meta charset="utf-8"></head>';
$body .= '<body style="margin:0;background:#f6f7f9;color:#17202a;font-family:Arial,sans-serif;line-height:1.5">';
$body .= '<div style="max-width:760px;margin:0 auto;padding:24px">';
$body .= '<div style="background:#212529;color:#fff;padding:18px 22px;border-radius:10px 10px 0 0"><strong>WT webtrash.ch</strong></div>';
$body .= '<div style="background:#fff;border:1px solid #d8dee8;border-top:0;padding:24px;border-radius:0 0 10px 10px">';
$body .= '<h1 style="margin:0 0 8px;font-size:26px">' . email_escape($title) . '</h1>';
$body .= '<p style="margin:0 0 18px;color:#5f6b7a">Du kannst die Druckansicht direkt im Browser öffnen oder den Wochenplan weiter unten als HTML ansehen.</p>';
$body .= '<p style="margin:0 0 24px">';
$body .= '<a href="' . email_escape($printUrl) . '" style="display:inline-block;background:#0d6efd;color:#fff;text-decoration:none;font-weight:bold;padding:12px 18px;border-radius:8px">Druckansicht öffnen</a>';
$body .= '</p>';

if ((string) $weekPlan['description'] !== '') {
    $body .= '<p style="margin:0 0 24px;color:#5f6b7a">' . nl2br(email_escape($weekPlan['description'])) . '</p>';
}

foreach (['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'] as $day) {
    if (empty($mealsByDay[$day])) {
        continue;
    }

    $body .= '<h2 style="margin:24px 0 8px;font-size:19px;border-bottom:2px solid #0d6efd;padding-bottom:6px">' . email_escape($day) . '</h2>';

    foreach ($mealsByDay[$day] as $meal) {
        $totalTime = (int) $meal['prep_time'] + (int) $meal['cook_time'];
        $body .= '<div style="margin:10px 0;padding:14px;background:#f6f7f9;border:1px solid #d8dee8;border-radius:8px">';
        $body .= '<div style="color:#5f6b7a;font-size:13px;font-weight:bold">' . email_escape($meal['meal_category']) . '</div>';
        $body .= '<h3 style="margin:3px 0 8px;font-size:17px">' . email_escape($meal['recipe_title']) . '</h3>';
        $body .= '<p style="margin:4px 0"><strong>Gesamtzeit:</strong> ' . $totalTime . ' Min.</p>';
        $body .= '<p style="margin:4px 0"><strong>Zutaten:</strong><br>' . nl2br(email_escape((string) $meal['ingredients'])) . '</p>';
        $body .= '<p style="margin:4px 0"><strong>Zubereitung:</strong><br>' . nl2br(email_escape((string) $meal['instructions'])) . '</p>';
        $body .= '</div>';
    }
}

$body .= '<p style="margin:26px 0 0;color:#5f6b7a;font-size:12px">Diese E-Mail wurde aus deinem Essensplan auf webtrash.ch gesendet.</p>';
$body .= '</div></div></body></html>';

$encodedSubject = '=?UTF-8?B?' . base64_encode($title) . '?=';
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'Content-Transfer-Encoding: 8bit',
    'From: Essensplan webtrash.ch <noreply@webtrash.ch>',
    'X-Mailer: PHP/' . PHP_VERSION,
];

if (!function_exists('mail') || !mail($user['email'], $encodedSubject, $body, implode("\r\n", $headers))) {
    redirect_with_email_flash($weekPlanId, 'error', 'Die E-Mail konnte vom Server nicht versendet werden.');
}

$_SESSION['week_email_last_sent_at'] = time();
$_SESSION['week_email_csrf'] = bin2hex(random_bytes(32));
redirect_with_email_flash($weekPlanId, 'success', 'Der Essensplan wurde an ' . $user['email'] . ' an den Mailserver übergeben.');
