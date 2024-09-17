<?php
require_once '../config/db.php';

if (isset($_GET['week_plan_id'])) {
    $db = new Database();
    $conn = $db->getConnection();

    $week_plan_id = $_GET['week_plan_id'];
    $stmt = $conn->prepare("UPDATE week_plan SET archived = 1 WHERE id = ?");
    $stmt->execute([$week_plan_id]);

    header("Location: ../index.php");
}
?>
